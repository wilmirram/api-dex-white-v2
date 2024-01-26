<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentOrderRequest;
use App\Models\Adm;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\PaymentOrder;
use App\Models\Product;
use App\Models\SendWhatsapp;
use App\Models\UserAccount;
use App\Models\User;
use App\Utils\Invoice;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function GuzzleHttp\json_encode;

class PaymentOrderController extends Controller
{
    private $payment;

    public function __construct(PaymentOrder $payment)
    {
        $this->payment = $payment;
    }

    public function index()
    {
        $data = $this->payment->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->payment->find($id);
        if(!$data){
            return (new Message())->defaultMessage(17, 404);
        }else{
            return response()->json($data);
        }
    }

    public function store(PaymentOrderRequest $request)
    {
        $data = $request->all();
        $payment = $this->payment->create($data);
        if($payment){
            return (new Message())->defaultMessage(21, 200, $payment);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $payment = $this->payment->find($id);
        if(!$payment){
            return (new Message())->defaultMessage(17, 404);
        }

        foreach ($request->all() as $key => $value) {
            DB::select("UPDATE PAYMENT_ORDER SET {$key} = '{$value}' WHERE id = {$id}");
        }

        return (new Message())->defaultMessage(22, 203);
    }

    
    public function approvePayment(PaymentOrderRequest $request)
    {
        //VERIFICAR A QUESTÃO DO TOKEN COM O MIGUEL
        $order = OrderItem::find($request->P_ORDER_ITEM_ID);
        if($order){
            $user = UserAccount::find($request->P_USER_ACCOUNT_ID);
            if($user){

                if((new JwtValidation())->validateByUser($request->P_ADM_ID, $request) == false){
                    return (new Message())->defaultMessage(41, 403);
                }

                $payment = PaymentMethod::find($request->P_PAYMENT_METHOD_ID);
                if($payment){
                    $adm = Adm::find($request->P_ADM_ID);
                    if($adm){
                        
                        $token = ['P_ORDER_ITEM_ID' => $request->P_ORDER_ITEM_ID,
                                  'P_USER_ACCOUNT_ID' => $request->P_USER_ACCOUNT_ID,
                                  'P_PAYMENT_METHOD_ID' => $request->P_PAYMENT_METHOD_ID,
                                  'P_ADM_UUID' => $adm->UUID,
                                  'P_HASH' => $request->P_HASH];

                        $token = json_encode($token);
                        $token = base64_encode($token);

                        $result = DB::select("CALL SP_APPROVE_PAYMENT(
                         '{$request->P_ORDER_ITEM_ID}',
                         '{$request->P_USER_ACCOUNT_ID}',
                         '{$request->P_PAYMENT_METHOD_ID}',
                         '{$token}',
                         '{$adm->UUID}',
                         '{$request->P_HASH}'
                         )");
                        if($result[0]->CODE == 1){
                            $owner = User::find($user->USER_ID);
                            if($owner->TYPE_DOCUMENT_ID == 1 || $owner->TYPE_DOCUMENT_ID == 3){
                                $name =  $owner->NAME;
                            }else{
                                $name =  $owner->SOCIAL_REASON;
                            }

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
                            try {
                                Invoice::generateOne($request->P_ORDER_ITEM_ID, true, true);
                                try {
                                    $user = User::find($order->USER_ID);
                                    $account = UserAccount::find($order->USER_ACCOUNT_ID);
                                    if(!$user){
                                        $account = UserAccount::find($order->USER_ACCOUNT_ID);
                                        $user = $account->USER_ID;
                                    }

                                    $data = [
                                        'WHATSAPP' => $user->DDI . $user->PHONE,
                                        'NAME' => $user->NAME,
                                        'USER_ID' => $user->ID,
                                        'USER_ACCOUNT_ID' => $account ? $account->ID : null,
                                        'COMBO' => Product::getComboName($order->PRODUCT_ID)
                                    ];

                                    SendWhatsapp::sendMessage($data, SendWhatsapp::CONFIRM_COMBO);
                                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                                }catch (\Exception $e){
                                    if(file_exists("log.txt")){
                                        $data = [
                                            'order' => [
                                                'WHATSAPP' => $user->DDI . $user->PHONE,
                                                'NAME' => $user->NAME,
                                                'USER_ID' => $user->ID,
                                                'USER_ACCOUNT_ID' => $account ? $account->ID : null,
                                                'COMBO' => Product::getComboName($order->PRODUCT_ID)
                                            ],
                                            'line' => $e->getLine(),
                                            'message' => $e->getMessage()
                                        ];
                                        $data = json_encode($data);
                                        $arquivo = fopen('log.txt','a+');
                                        fwrite($arquivo, $data);
                                        fclose($arquivo);
                                    }else {
                                        $data = [
                                            'order' => [
                                                'WHATSAPP' => $user->DDI . $user->PHONE,
                                                'NAME' => $user->NAME,
                                                'USER_ID' => $user->ID,
                                                'USER_ACCOUNT_ID' => $account ? $account->ID : null,
                                                'COMBO' => Product::getComboName($order->PRODUCT_ID)
                                            ],
                                            'line' => $e->getLine(),
                                            'message' => $e->getMessage()
                                        ];
                                        $arquivo = fopen('log.txt','w');
                                        fwrite($arquivo, $data);
                                        fclose($arquivo);
                                    }
                                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                                }
                            }catch (\Exception $e){
                                try {
                                    $account = UserAccount::find($order->USER_ACCOUNT_ID);
                                    $user = User::find($account->USER_ID);

                                    $data = [
                                        'WHATSAPP' => $user->DDI . $user->PHONE,
                                        'NAME' => $user->NAME,
                                        'USER_ID' => $user->ID,
                                        'USER_ACCOUNT_ID' => $account ? $account->ID : null,
                                        'COMBO' => Product::getComboName($order->PRODUCT_ID)
                                    ];

                                    SendWhatsapp::sendMessage($data, SendWhatsapp::CONFIRM_COMBO);
                                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                                }catch (\Exception $e){
                                    if(file_exists("log.txt")){
                                        $data = [
                                            'order' => [
                                                'WHATSAPP' => $user->DDI . $user->PHONE,
                                                'NAME' => $user->NAME,
                                                'USER_ID' => $user->ID,
                                                'USER_ACCOUNT_ID' => $account ? $account->ID : null,
                                                'COMBO' => Product::getComboName($order->PRODUCT_ID)
                                            ],
                                            'line' => $e->getLine(),
                                            'message' => $e->getMessage()
                                        ];
                                        $data = json_encode($data);
                                        $arquivo = fopen('log.txt','a+');
                                        fwrite($arquivo, $data);
                                        fclose($arquivo);
                                    }else {
                                        $data = [
                                            'order' => [
                                                'WHATSAPP' => $user->DDI . $user->PHONE,
                                                'NAME' => $user->NAME,
                                                'USER_ID' => $user->ID,
                                                'USER_ACCOUNT_ID' => $account ? $account->ID : null,
                                                'COMBO' => Product::getComboName($order->PRODUCT_ID)
                                            ],
                                            'line' => $e->getLine(),
                                            'message' => $e->getMessage()
                                        ];
                                        $arquivo = fopen('log.txt','w');
                                        fwrite($arquivo, $data);
                                        fclose($arquivo);
                                    }
                                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                                }
                            }
                        }elseif($result[0]->CODE == 15){
                            return (new Message())->defaultMessage(15, 400);
                        }else{
                            return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_APPROVE_PAYMENT');
                        }
                    }else{
                        return (new Message())->defaultMessage(27, 404);
                    }
                }else{
                    return (new Message())->defaultMessage(29, 404);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(28, 404);
        }
    }
}
