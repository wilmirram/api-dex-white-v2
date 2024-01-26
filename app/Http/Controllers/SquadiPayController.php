<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferBalanceAccountRequest;
use App\Models\SquadiPay;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserAccount;
use App\Utils\Invoice;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class SquadiPayController extends Controller
{
    private $token;

    public function __construct(SquadiPay $squadiPay)
    {
        $this->token = $squadiPay->getType()." ".$squadiPay->getToken();
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), ['ORDER_ITEM_ID' => 'required'])->validate();
        $orderId = $request->ORDER_ITEM_ID;
        $paymentmethod = $request->PAYMENT_METHOD_ID;
        $order = OrderItem::find($request->ORDER_ITEM_ID);

        if($order){

            /*
            if($order->STATUS_ORDER_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'THIS ORDER CAN NOT BE CHANGED']], 400);
            }
            */
            if((new JwtValidation())->validateByUserAccount($order->USER_ACCOUNT_ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            /*
            if($response->status() == 200){
            */

             //   $json = json_decode($response->body());
                $result = DB::select("CALL SP_REGISTER_ORDER({$request->ORDER_ITEM_ID},{$request->PAYMENT_METHOD_ID})");
                if($result[0]->CODE == 1){

                    if ($request->PAYMENT_METHOD_ID == 5) {
                    // VERIFICAR SE É ATIVAÇÃO INTERNA
                    $result2 = DB::select("CALL SP_APPROVE_PAYMENT({$request->ORDER_ITEM_ID},$order->USER_ACCOUNT_ID,5,'P_TOKEN','c6cc3204-2776-11ee-b5b0-c6d52e6ea542','INTERNAL' )");

                        return ($result2);

                    }
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_REGISTER_ORDER');
                }
            /*
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => 'IT WAS NOT POSSIBLE TO PROCESS YOUR REQUEST, TRY AGAIN OR CONTACT SUPPORT']], 400);
            }
            */
        }else{
            return response()->json(['ERROR' => ['MESSAGE' => 'ORDER ITEM NOT FOUND']], 404);
        }
    }

    public function clearCryptoOrder($id, Request $request)
    {
        $order = OrderItem::find($id);
        if($order){
            if($order->STATUS_ORDER_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'THIS ORDER CAN NOT BE CHANGED']], 400);
            }

            if((new JwtValidation())->validateByUserAccount($order->USER_ACCOUNT_ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            DB::select("UPDATE ORDER_ITEM SET CRYPTO_ADDRESS = NULL, CRYPTO_DT_REGISTER = NULL, CRYPTO_BILLET_NUMBER = NULL, CRYPTO_BILLET_NUMBER = NULL,CRYPTO_CURRENCY_ID = NULL, CRYPTO_QUOTE_BRL = NULL, CRYPTO_AMOUNT = NULL, CRYPTO_QR_CODE = NULL, PAYMENT_METHOD_ID = NULL WHERE id = {$id}");

            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => ['MESSAGE' => 'ORDER ITEM NOT FOUND']], 404);
        }
    }

    public function callback(Request $request)
    {
        //token: E2FE9EB29A47F4FCF5B37D3F9D8D872EF43372FEBE2CD2
        if(!$request->hasHeader('token') ||
            strlen($request->header('token')) != 46 ||
            $request->header('token') != env("CALLBACK_TOKEN")){
            return response()->json(['ERROR' => ["MESSAGE" => "ACCESS DENIED"]], 403);
        }

        Validator::make($request->all(), [
            'transaction' => 'required'
        ])->validate();

        Validator::make($request->transaction, [
            "amount" => 'required',
            "updatedAt" => 'required',
            "transactionHash" => 'required',
            "currentPaidAmount" => 'required',
            "currentStatus" => 'required',
            "notificationStatus" => 'required',
            "billetNumber" => 'required'
        ])->validate();

        if(file_exists("log.txt")){
            $data = $request->all();
            $data = json_encode($data['transaction']);
            $arquivo = fopen('log.txt','a+');
            fwrite($arquivo, $data);
            fclose($arquivo);
        }else {
            $data = $request->all();
            $data = json_encode($data['transaction']);
            $arquivo = fopen('log.txt','w');
            fwrite($arquivo, $data);
            fclose($arquivo);
        }

        $updatedAt = date('Y-m-d H:i:s', strtotime($request->transaction['updatedAt']));

        $order = OrderItem::where("CRYPTO_BILLET_NUMBER", $request->transaction['billetNumber'])->first();
        if($order){

            $quotation = Http::get("https://blockchain.info/tobtc?currency=BRL&value=1"); //VERIFICA A COTAÇÃO DO BTC
            $quotation = $quotation->body();

            $result = DB::select("CALL SP_APPROVE_CRYPTO_PAYMENT(
                                                                       {$order->CRYPTO_CURRENCY_ID},
                                                                       '{$request->transaction['amount']}',
                                                                       '{$updatedAt}',
                                                                       '{$request->transaction['transactionHash']}',
                                                                       '{$request->transaction['currentPaidAmount']}',
                                                                       '{$request->transaction['currentStatus']}',
                                                                       '{$request->transaction['notificationStatus']}',
                                                                       '{$request->transaction['billetNumber']}',
                                                                       '{$quotation}',
                                                                       '356a192b7913b04c54574d18c28d46e6395428ab'
                                                                       )");

            if($result[0]->CODE == 1){
                $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
                $owner = User::find($userAccount->USER_ID);

                if($owner->TYPE_DOCUMENT_ID == 1 || $owner->TYPE_DOCUMENT_ID == 3){
                    $name =  $owner->NAME;
                }else{
                    $name =  $owner->SOCIAL_REASON;
                }
                /*
                if ($order->PRODUCT_ID == 12){
                    $response = Http::post(env('VG_SCHOOL')."/api/register-for-api", [
                        'first_name' => '',
                        'last_name' => $name,
                        'email' => $owner->EMAIL,
                        'password' => $owner->ID.$owner->EMAIL,
                        'flag' => 1.5,
                        'phone' => $owner->DDI. ' ' . $owner->PHONE,
                        'address' => $owner->ADDRESS,
                        'city' => $owner->CITY,
                        'state' => $owner->STATE,
                    ]);
                }elseif($order->PRODUCT_ID == 13) {
                    $response = Http::post(env('VG_SCHOOL')."/api/register-for-api", [
                        'first_name' => '',
                        'last_name' => $name,
                        'email' => $user->EMAIL,
                        'password' => $user->ID.$user->EMAIL,
                        'flag' => 9,
                        'phone' => $user->DDI. ' ' . $user->PHONE,
                        'address' => $user->ADDRESS,
                        'city' => $user->CITY,
                        'state' => $user->STATE,
                    ]);
                }else{
                    $response = Http::post(env('VG_SCHOOL')."/api/register-for-api", [
                        'first_name' => '',
                        'last_name' => $name,
                        'email' => $owner->EMAIL,
                        'password' => $owner->ID.$owner->EMAIL,
                        'flag' => $order->PRODUCT_ID,
                        'phone' => $owner->DDI. ' ' . $owner->PHONE,
                        'address' => $owner->ADDRESS,
                        'city' => $owner->CITY,
                        'state' => $owner->STATE,
                    ]);

                }
                */
                Invoice::generateOne($order->ID, true, true);
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_APPROVE_CRYPTO_PAYMENT');
            }
        }else{
            return (new Message())->defaultMessage(14, 404);
        }
    }
}
