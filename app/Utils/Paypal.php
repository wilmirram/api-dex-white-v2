<?php


namespace App\Utils;


use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function GuzzleHttp\json_decode;

class Paypal
{
    private $clientId = 'AZyAmypHRSQaVv1uQAVoR5ajA9JHiG2eKhOuckpZjpNxHMKe1L3bdzKQT-uQNbMInQ0FJh3_BQFyqyB1';
    private $secret = 'EFD0-1zI9pVCRMCuoeCB5vPXvU1fUUyvVMAazcdFN7amELWNL6BxnyOUH3vhB9tnPPQEjqXZrKmGa8Gv';
    private $url;
    private $baseUrl = 'https://api-m.sandbox.paypal.com/';
    private $content;
    private $accessToken;
    private $appId;
    private $tokenType;

    public function getToken()
    {
        $this->url = $this->baseUrl . 'v1/oauth2/token';
        $this->content = ["grant_type" => "client_credentials"];

        $response = Http::asForm()->withBasicAuth($this->clientId, $this->secret)->post($this->url, $this->content);

        $data = json_decode($response->body());

        $this->accessToken = $data->access_token;
        $this->appId = $data->app_id;
        $this->tokenType = $data->token_type;
    }

    public function makeTransaction($order)
    {
        $this->getToken();

        $items = [];

        $account = $order->USER_ACCOUNT_ID ? $order->USER_ACCOUNT_ID : 'NULL';

        $productList = DB::select("CALL SP_GET_VS_ORDER_ITEM_LIST_EXTERNAL({$order->USER_ID}, {$account}, {$order->ID})");

        $user = User::find($order->USER_ID);
        $total = 0;
        foreach ($productList as $item){
            array_push($items, [
                "name" => $item->PRODUCT_NAME,
                "description" => $item->PRODUCT,
                "quantity" => $item->UNITS,
                "price" => (double) $item->SALE_PRICE_DISCOUNT,
                "sku" => $item->VS_PRODUCT_ID,
                "currency" => "BRL",
                "tax" => "0.00",
            ]);
            $total += (double) $item->SALE_PRICE_DISCOUNT;
        }

        $this->content = [
            "intent" => "sale",
            "payer" => [
                    "payment_method" => "paypal"
            ],
            "transactions" => [
                [
                    "amount" => [
                        "currency" => "BRL",
                        "total" => (string) $order->NET_PRICE,
                        "details" => [
                            "shipping" => (string) $order->SHIPPING_COST,
                            "shipping_discount" => "0.00",
                            "subtotal" => (string)  $order->NET_PRICE - $order->SHIPPING_COST,
                            "insurance" =>"0.00",
                            "handling_fee" =>"0.00",
                            "tax" =>"0.00"
                        ]
                    ],
                    "description" => $order->ID,
                    "payment_options" => [
                            "allowed_payment_method" => "IMMEDIATE_PAY"
                    ],
                    "item_list" => [
                        "shipping_address" => [
                            "recipient_name" => "PP Plus Recipient",
                            "line1" => $user->ADDRESS . ', ' . $user->NUMBER . ', ' . $user->NEIGHBORHOOD,
                            "line2" => $user->COMPLEMENT ?: '',
                            "city" => (string) $user->CITY,
                            "country_code" => "BR",
                            "postal_code" => (string)$user->ZIP_CODE,
                            "state" => $user->STATE,
                            "phone" => (string) str_replace([' ', '-'], ['', ''], $user->PHONE)
                        ],
                        "items" => $items
                    ]
                ]
            ],
            "redirect_urls" => [
                "return_url" => "https://example.com/return",
                "cancel_url" => "https://example.com/cancel"
            ]
        ];

        $this->url = $this->baseUrl . "v1/payments/payment";

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer $this->accessToken"
        ])->post($this->url, $this->content);

        return json_decode($response->body());
    }

    public function payment($data)
    {
        $this->getToken();

        $this->url = $this->baseUrl . 'v1/payments/payment/' . $data["payment_id"] . "/execute/";

        $this->content = [
            "payer_id" => $data['payer_id']
        ];

        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->accessToken",
            "Content-Type" => "application/json"
        ])->post($this->url, $this->content);

        return $response->body();
    }
}
