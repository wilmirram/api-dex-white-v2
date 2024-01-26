<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\VS\Order;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Mailgun\Exception;

class PixController extends Controller
{
    private $boleto;

    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
        $this->boleto->token = "df28c876-9f6e-11ea-bb37-0242ac130002";
        $this->boleto->url = "https://prd-api.u4cdev.com";
    }

    public function generateForStore(Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required'
        ])->validate();

        $order = Order::find($request->VS_ORDER_ID);

        if (! $order) {
            return (new Message())->defaultMessage(17, 400);
        }

        $userId = $order->USER_ID;

        if (! $order) {
            $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
            $userId = $userAccount->USER_ID;
        }

        $user = User::find($userId);

        $body = [
            "customerId" => "c7307cf7-f450-46e1-826c-edb06f37da74",
            "externalId" =>  (string) $order->ID,
            "additionalInformation" => [
                [
                    "content" => "descrição do pix"
                ]
            ],
            "dynamicQRCodeType" => "BILLING_DUE_DATE",
            "billingDueDate" => [
                "dueDate" => date('Y-m-d', strtotime(date('Y-m-d') . " + 1 day")),
                "daysAfterDueDate" => 102,
                "payerInformation" => [
                    "name" => $user->NAME,
                    "cpfCnpj" => $user->DOCUMENT,
                    "addressing" => [
                        "street" => $user->ADDRESS,
                        "city" => $user->CITY,
                        "uf" => $user->STATE,
                        "cep" => $user->ZIP_CODE
                    ]
                ],
                "paymentValue" => [
                    "documentValue" => $order->NET_PRICE,
                ]
            ]];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
            'token' => $this->boleto->getToken(),
            'partner' => $this->boleto->getPartner()
        ])->post("{$this->boleto->url}/pix/brcode/erp/dynamic",
            $body
        );

        if($response->status() >= 200 && $response->status() < 300){

            $result = json_decode($response->body());

            $return = [
                'id' => $result->itemId,
                'content' => $result->data->textContent,
                'image' => "data:".$result->data->generatedImage->mimeType.";base64,".$result->data->generatedImage->imageContent,
                'external_id' => $result->externalId
            ];

            try {
                DB::select("
                        UPDATE VS_ORDER SET
                            PIX_ID = '{$result->itemId}',
                            PAYMENT_METHOD_ID = 6,
                            DIGITAL_PLATFORM_ID = 1
                        WHERE ID = {$order->ID}
                        ");

                return (new Message())->defaultMessage(1, 200, $return);
            }catch (\Exception $e){
                return response()->json(['ERROR' => ["MESSAGE" => "PIX ERROR"]], 400);
            }
        }

        return response()->json(['ERROR' => ["MESSAGE" => "PIX ERROR"]], 400);
    }

    public function generate(Request $request)
    {
        Validator::make($request->all(), [
            'ORDER_ID' => 'required'
        ])->validate();

        $order = OrderItem::find($request->ORDER_ID);

        if (! $order) {
            return (new Message())->defaultMessage(17, 400);
        }

        $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
        $user = User::find($userAccount->USER_ID);

        $body = [
            "customerId" => "c7307cf7-f450-46e1-826c-edb06f37da74",
            "externalId" =>  (string) $order->ID,
            "additionalInformation" => [
                [
                    "content" => "descrição do pix"
                ]
            ],
            "dynamicQRCodeType" => "BILLING_DUE_DATE",
            "billingDueDate" => [
            "dueDate" => date('Y-m-d', strtotime(date('Y-m-d') . " + 1 day")),
            "daysAfterDueDate" => 102,
            "payerInformation" => [
                "name" => $user->NAME,
                "cpfCnpj" => $user->DOCUMENT,
                "addressing" => [
                    "street" => $user->ADDRESS,
                    "city" => $user->CITY,
                    "uf" => $user->STATE,
                    "cep" => $user->ZIP_CODE
                ]
            ],
            "paymentValue" => [
                "documentValue" => $order->NET_PRICE * 5,
            ]
        ]];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
            'token' => $this->boleto->getAuthorization(),
            'partner' => $this->boleto->getPartner()
        ])->post("{$this->boleto->url}/pix/brcode/erp/dynamic",
            $body
        );

        if($response->status() >= 200 && $response->status() < 300){

            $result = json_decode($response->body());

            $return = [
                'id' => $result->itemId,
                'content' => $result->data->textContent,
                'image' => "data:".$result->data->generatedImage->mimeType.";base64,".$result->data->generatedImage->imageContent,
                'external_id' => $result->externalId
            ];

            try {
                DB::select("
                        UPDATE ORDER_ITEM SET
                            PIX_ID = '{$result->itemId}',
                            PAYMENT_METHOD_ID = 6,
                            DIGITAL_PLATFORM_ID = 1
                        WHERE ID = {$order->ID}
                        ");

                return (new Message())->defaultMessage(1, 200, $return);
            }catch (\Exception $e){
                return response()->json(['ERROR' => ["MESSAGE" => "PIX ERROR"]], 400);
            }
        }

        return response()->json(['ERROR' => ["MESSAGE" => "PIX ERROR"]], 400);
    }

    public function cancel($id)
    {
        $order = OrderItem::find($id);

        if (! $order) {
            return (new Message())->defaultMessage(17, 400);
        }

        try {
            DB::select("
                        UPDATE ORDER_ITEM SET
                            PIX_ID = NULL,
                            PAYMENT_METHOD_ID = NULL,
                            DIGITAL_PLATFORM_ID = NULL
                        WHERE ID = {$order->ID}
            ");

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ["MESSAGE" => "PIX ERROR"]], 400);
        }
    }

    public function callback(Request $request)
    {
        if(!$request->hasHeader('token') ||
            strlen($request->header('token')) != 46 ||
            $request->header('token') != env("CALLBACK_TOKEN")){
            return response()->json(['ERROR' => ["MESSAGE" => "ACCESS DENIED"]], 403);
        }

        Validator::make($request->all(), [
           'id' => 'required',
           'externalId' => 'required'
        ])->validate();

        $result = DB::select("CALL SP_APPROVE_AUTOMATIC_PAYMENT_PIX ('{$request->id}', 1, '77de68daecd823babbb58edb1c8e14d7106e83bb')");
        if($result[0]->CODE == 1){
            return (new Message())->defaultMessage(1, 200);
        }

        return (new Message())->defaultMessage($result[0]->CODE, 400);
    }
}
