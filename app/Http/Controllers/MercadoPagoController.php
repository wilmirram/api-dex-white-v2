<?php

namespace App\Http\Controllers;
// ramires
use App\Models\Market\CreditCardCallback;
use App\Models\Market\CreditCardLog;
use App\Models\User;
use App\Models\VS\Order;
use App\Utils\CreditCard;
use App\Utils\Message;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\json_encode;
use MercadoPago;

class MercadoPagoController extends Controller
{

    public function index(Request $request){
      $order = $request->all();
      $order = json_encode($order);
      $order = json_decode($order);
      $products = array();
      MercadoPago\SDK::setAccessToken(env('ACCESS_TOKEN_MERCADOPAGO',''));
      $preference = new MercadoPago\Entities\Preference();
      $preference->back_urls = array(
        "success"=> $order->success,
        "failure"=> $order->failure,
        "pending"=> $order->pending
      );
      $preference->external_reference = $order->external_reference;
      $preference->notification_url= "https://whiteclub.tech/api/ipn-mercadopago";
      $order = OrderItem::where('ID',$order->external_reference)->first();
      $product = Product::where('ID',$order->PRODUCT_ID)->first();

      $item = new MercadoPago\Entities\Shared\Item();
      $item->id = $product->ID;
      $item->title = $product->NAME;
      $item->description = $product->DESCRIPTION;
      $item->quantity = 1;
      $item->unit_price = intval($order->NET_PRICE*env('DOLAR'));
      array_push($products,$item );

      //dd($products);
      $preference->items = $products;
      $preference->save();
      echo $preference->init_point;
    }



    public function ipn(Request $request){
    header("Status: 200");
    $merchant_id = $_GET['id']; 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/merchant_orders/'.$merchant_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array();
    $headers[] = 'Authorization: Bearer '.env('ACCESS_TOKEN_MERCADOPAGO');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    $result = json_decode($result);
    $external_ref = $result->external_reference;
    if($result->status == 'closed'){
        $result = $result->payments[0]->status;
        $order = OrderItem::find($external_ref);

        if($result == 'approved'){
            $order->STATUS_ORDER_ID = 2;
        }
        if($result == 'in_process'){
            $order->STATUS_ORDER_ID = 1;
        }
    
        if($result == 'rejected'){
            $order->STATUS_ORDER_ID = 3;
        }
    
        $order->save();
    }
    curl_close($ch);
    $order = OrderItem::find($merchant_id);
    }

    public function getPublicKey()
    {
        return (new Message())->defaultMessage(1, 200, CreditCard::public_key);
    }

    public function storeApi(Request $request)
    {
        $data = $request->all();
        Validator::make($data, [
            "VS_ORDER_ID" => 'required',
            "transaction_amount" => 'required',
            "token" => 'required',
            "description" => 'required',
            "installments" => 'required',
            "payment_method_id" => 'required',
            "issuer_id" => 'required',
            'payer' => 'required'
        ])->validate();

        Validator::make($data['payer'], [
            'email' => 'required',
            'identification' => 'required'
        ])->validate();

        Validator::make($data['payer']['identification'], [
            'type' => 'required',
            'number' => 'required'
        ])->validate();

        $order = Order::find($request->VS_ORDER_ID);

        if ($order->CREDIT_CARD_TRANSACTION_ID){
            return response()->json(['ERROR' => ['DATA' => 'THIS ORDER ALREADY HAVE A CREDIT CARD TRANSACTION']], 400);
        }

        $user = User::find($order->USER_ID);

        $mercadoPago = CreditCard::paymentApi($data, $user, $order);

        if ($mercadoPago['id'] == null) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT', 'payment' => $mercadoPago['payment']]], 400);

        $status = ((DB::select("SELECT STATUS_DETAIL_BR as result FROM CREDIT_CARD_STATUS WHERE STATUS_DETAIL = '{$mercadoPago['status_detail']}'")));
        if (array_key_exists(0, $status)){
            $status = $status[0]->result;
        }else{
            $status = 'TRY AGAIN OR CONTACT THE SUPPORT';
        }

        if ($mercadoPago['status'] != "approved" && $mercadoPago['status'] != "in_process" && $mercadoPago['status'] != "in_process"){
            return response()->json(['ERROR' => ['DATA' => $status]], 400);
        }

        $json = [];
        $json['OPERATION'] = 'UPDATE';
        $json['CREDIT_CARD_OPERATOR_ID'] = 1;
        $json['STATUS_ORDER_ID'] = 7;
        $json['CREDIT_CARD_TRANSACTION_ID'] = $mercadoPago['id'];
        $json['PAYMENT_METHOD_ID'] = 5;
        $json['ADM_ID'] = 15;
        $json['ID'] = $request->VS_ORDER_ID;

        $json = json_encode($json);

        $result = DB::select("CALL SP_UPDATE_VS_ORDER ('{$json}', '4c1723ee-00fa-11ec-92f1-86944fa96cf0', @P_CODE_LIST_ID)");
        $select = DB::select("SELECT @P_CODE_LIST_ID as code");

        if($select[0]->code == 1){
            return (new Message())->defaultMessage(1, 200,$mercadoPago['payment']);
        }else{
            return (new Message())->defaultMessage($select[0]->code, 400, null, 'SP_UPDATE_VS_ORDER');
        }
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Validator::make($data, [
            "VS_ORDER_ID" => 'required',
            "transaction_amount" => 'required',
            "token" => 'required',
            "description" => 'required',
            "installments" => 'required',
            "payment_method_id" => 'required',
            "issuer_id" => 'required',
            'payer' => 'required'
        ])->validate();

        Validator::make($data['payer'], [
            'email' => 'required',
            'identification' => 'required'
        ])->validate();

        Validator::make($data['payer']['identification'], [
            'type' => 'required',
            'number' => 'required'
        ])->validate();

        $order = Order::find($request->VS_ORDER_ID);

        if ($order->CREDIT_CARD_TRANSACTION_ID){
            return response()->json(['ERROR' => ['DATA' => 'THIS ORDER ALREADY HAVE A CREDIT CARD TRANSACTION']], 400);
        }

        $user = User::find($order->USER_ID);

        $mercadoPago = CreditCard::payment($data, $user, $order);

        if ($mercadoPago['id'] == null) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT', 'PAYMENT' => $mercadoPago['payment']]], 400);

        $status = ((DB::select("SELECT STATUS_DETAIL_BR as result FROM CREDIT_CARD_STATUS WHERE STATUS_DETAIL = '{$mercadoPago['status_detail']}'")));
        if (array_key_exists(0, $status)){
            $status = $status[0]->result;
        }else{
            $status = 'TRY AGAIN OR CONTACT THE SUPPORT';
        }

        if ($mercadoPago['status'] != "approved" && $mercadoPago['status'] != "in_process" && $mercadoPago['status'] != "in_process"){
            return response()->json(['ERROR' => ['DATA' => $status, 'PAYMENT' => $mercadoPago['payment']]], 400);
        }

        $json = [];
        $json['OPERATION'] = 'UPDATE';
        $json['CREDIT_CARD_OPERATOR_ID'] = 1;
        $json['STATUS_ORDER_ID'] = 7;
        $json['CREDIT_CARD_TRANSACTION_ID'] = $mercadoPago['id'];
        $json['PAYMENT_METHOD_ID'] = 5;
        $json['ADM_ID'] = 15;
        $json['ID'] = $request->VS_ORDER_ID;

        $json = json_encode($json);

        $result = DB::select("CALL SP_UPDATE_VS_ORDER ('{$json}', '4c1723ee-00fa-11ec-92f1-86944fa96cf0', @P_CODE_LIST_ID)");
        $select = DB::select("SELECT @P_CODE_LIST_ID as code");

        if($select[0]->code == 1){
            return (new Message())->defaultMessage(1, 200, $mercadoPago['payment']);
        }else{
            return (new Message())->defaultMessage($select[0]->code, 400, $mercadoPago['payment'], 'SP_UPDATE_VS_ORDER');
        }
    }

    public function cancel(Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required'
        ])->validate();

        $order = Order::find($request->VS_ORDER_ID);
        if (! $order) return response()->json(['ERROR' => ['DATA' => 'ORDER NOT FOUND']], 404);

        //if ($order->PAYMENT_METHOD_ID != 5) return response()->json(['ERROR' => ['DATA' => 'THIS IS NOT A CREDIT CARD ORDER']], 400);

        try {
            DB::select("
                        UPDATE VS_ORDER SET
                                            PAYMENT_METHOD_ID = NULL,
                                            CREDIT_CARD_TRANSACTION_ID = NULL,
                                            CREDIT_CARD_OPERATOR_ID = NULL
                        WHERE ID = {$order->ID}
            ");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);
        }
    }

    public function callback(Request $request)
    {

        $creditCardCallback = new CreditCardCallback();
        $creditCardCallback->create([
            'CREDIT_CARD_OPERATOR_ID' => 1,
            'CREDIT_CARD_TRANSACTION_ID' => $request->has('data') ? $request->data['id'] : 0,
            'CREDIT_CARD_TRANSACTION_JSON' => json_encode($request->all()),
            'ADM_ID' => 15
        ]);

        if ($request->has('type') && $request->type !== 'payment') return response()->json(['data' => 'success']);

        if ($request->has('action') && ($request->action === "payment.updated" || $request->action === "payment.created")){
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.CreditCard::access_token,
            ])->get("https://api.mercadopago.com/v1/payments/{$request->data['id']}");

            if($response->status() == 200){
                $body = json_decode($response->body());
                $payment = new \stdClass();

                $payment->id = $body->id;
                $payment->status = $body->status;
                $payment->status_details = $body->status_detail;
                $payment->external_reference = $body->external_reference;
                $payment->payment_details = [
                    'installments' => $body->installments,
                    'fee' => isset($body->fee_details[0]->amount) ? $body->fee_details[0]->amount : 0,
                    'installment_amount' => $body->transaction_details->installment_amount,
                    'net_received_amount' => $body->transaction_details->net_received_amount,
                    'total_paid_amount' => $body->transaction_details->total_paid_amount,
                    'transaction_amount' => (double) $body->transaction_amount
                ];
                $payment->card = [
                    'expiration_month' =>$body->card->expiration_month,
                    'expiration_year' =>$body->card->expiration_year,
                    'first_six_digits' =>$body->card->first_six_digits,
                    'last_four_digits' =>$body->card->last_four_digits,
                    'brand' => $body->payment_method_id,
                    'cardholder' => [
                        'doc' => $body->card->cardholder->identification->number,
                        'type' => $body->card->cardholder->identification->type,
                        'name' => $body->card->cardholder->name
                    ]
                ];

                if ($payment->status === "approved"){
                    try {
                        $order = Order::find($payment->external_reference);
                        $userAccount = $order->USER_ACCOUNT_ID ? $order->USER_ACCOUNT_ID : 'NULL';
                        $transactionJson = json_encode($body);
                        $result = DB::select("CALL SP_VS_APPROVE_PAYMENT_CREDIT_CARD(
                            {$order->USER_ID},
                            {$userAccount},
                            {$order->ID},
                            1,
                            5,
                            '{$payment->id}',
                            '{$transactionJson}',
                            '{$payment->payment_details['transaction_amount']}',
                            '4c1723ee-00fa-11ec-92f1-86944fa96cf0'
                        )");
                        if ($result[0]->CODE != 1){
                            $creditCardLog = new CreditCardLog();

                            $status = ((DB::select("SELECT ID as result FROM CREDIT_CARD_STATUS WHERE STATUS = '{$payment->status}'")));

                            $creditCardStatus = $status[0]->result;

                            $creditCardLog->create([
                                'CREDIT_CARD_OPERATOR_ID' => 1,
                                'CREDIT_CARD_TRANSACTION_ID' => $request->data['id'],
                                'CREDIT_CARD_TRANSACTION_JSON' => json_encode($body),
                                'VS_ORDER_ID' => $payment->external_reference,
                                'CREDIT_CARD_STATUS_ID' => $creditCardStatus,
                                'ADM_ID' => 15,
                                'CODE_LIST_ID' => $result[0]->CODE
                            ]);
                        }
                        return response()->json(['data' => 'success']);
                    }catch (\Exception $e){
                        $creditCardLog = new CreditCardLog();

                        $status = ((DB::select("SELECT ID as result FROM CREDIT_CARD_STATUS WHERE STATUS = '{$payment->status}'")));

                        $creditCardStatus = $status[0]->result;

                        $creditCardLog->create([
                            'CREDIT_CARD_OPERATOR_ID' => 1,
                            'CREDIT_CARD_TRANSACTION_ID' => $request->data['id'],
                            'CREDIT_CARD_TRANSACTION_JSON' => json_encode($body),
                            'VS_ORDER_ID' => $payment->external_reference,
                            'CREDIT_CARD_STATUS_ID' => $creditCardStatus,
                            'ADM_ID' => 15
                        ]);
                        return response()->json(['data' => 'success']);
                    }

                }else{
                    try {

                        if ($payment->status === "rejected"){
                            DB::select("UPDATE VS_ORDER SET STATUS_ORDER_ID = 8 WHERE ID = {$payment->external_reference}");
                        }

                        $creditCardLog = new CreditCardLog();

                        $status = ((DB::select("SELECT ID as result FROM CREDIT_CARD_STATUS WHERE STATUS = '{$payment->status}'")));

                        $creditCardStatus = $status[0]->result;

                        $creditCardLog->create([
                            'CREDIT_CARD_OPERATOR_ID' => 1,
                            'CREDIT_CARD_TRANSACTION_ID' => $request->data['id'],
                            'CREDIT_CARD_TRANSACTION_JSON' => json_encode($body),
                            'VS_ORDER_ID' => $payment->external_reference,
                            'CREDIT_CARD_STATUS_ID' => $creditCardStatus,
                            'ADM_ID' => 15
                        ]);
                        return response()->json(['data' => 'success']);
                    }catch (\Exception $exception){
                        return response()->json(['data' => 'success']);
                    }
                }
            }
        }

        return response()->json(['data' => 'success']);
    }
}
