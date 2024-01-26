<?php


namespace App\Utils;
use App\Models\MercadoPagoCustomer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use MercadoPago;


class CreditCard
{
    /** PROD */
    public const access_token = "APP_USR-7609142863167927-090816-fda2f6fd1d9045ce440bf00aecbbb6ad-443029640";
    public const public_key = "APP_USR-b0c8981e-b13d-479f-8590-8b0f2887e888";
    /** HOM */
    //public const access_token = "APP_USR-3894371941275286-090312-44aae7bb0afb95398fdf53667cc66741-791478955";
    //public const public_key = "APP_USR-a6b1d683-8d3f-44e1-b724-e8e368fb3d9b";
    /** PERSONAL */
    //public const access_token = "APP_USR-2190966787333055-071922-293efc579a1fffc533efabb2ca59a047-167920257";
    //public const public_key = "APP_USR-a099be67-608d-4f1c-aea5-d1bfc7d23dde";
    //public const access_token = "TEST-5290526054712787-090820-7adfdd7357a3d3ec4c0842042e80398e-821148505";
    //public const public_key = "TEST-feaad6ca-75e0-4e40-861c-12fb3fbb53b1";

    public static function paymentApi($data, $user, $order)
    {
        $name = explode(' ', $user->NAME);

        $account = $order->USER_ACCOUNT_ID ? $order->USER_ACCOUNT_ID : 'NULL';

        $items = [];

        $productList = DB::select("CALL SP_GET_VS_ORDER_ITEM_LIST_EXTERNAL({$order->USER_ID}, {$account}, {$order->ID})");

        foreach ($productList as $item){
            array_push($items, [
                "id" => $item->VS_PRODUCT_ID,
                "title" => $item->PRODUCT_NAME,
                "description" => $item->PRODUCT,
                "category_id" => $item->VS_CATEGORY,
                "quantity" => $item->UNITS,
                "unit_price" => (double) $item->SALE_PRICE_DISCOUNT
            ]);
        }

        $response = Http::withHeaders([
            'x-meli-session' => $data['device_id'],
            'Authorization' => 'Bearer '.CreditCard::access_token,
        ])->post("https://api.mercadopago.com/v1/payments", [
            "additional_info" => [
                "items" => $items
            ],
            "transaction_amount" => (float) $data['transaction_amount'],
            "token" => $data['token'],
            "description" => $data['description'],
            "installments" => (int)$data['installments'],
            "payment_method_id" => $data['payment_method_id'],
            "issuer_id" => (int)$data['issuer_id'],
            "external_reference" => $data['VS_ORDER_ID'],
            "payer" => [
                "first_name" =>$name[0],
                "last_name" => $name[count($name) - 1],
                "email" => $data['payer']['email'],
                "identification" => [
                    "type" => $data['payer']['identification']['type'],
                    "number" => $data['payer']['identification']['number']
                ],
                "address" => [
                    "street_name" => (string) $user->ADDRESS,
                    "street_number" => (string) $user->NUMBER,
                    "zip_code" => (string) $user->ZIP_CODE
                ]
            ],
        ]);
        $payment = json_decode($response->body());

        try {
            return array(
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'id' => $payment->id,
                'payment' => $payment
            );
        }catch (\Exception $e){
            return array(
                'status' => null,
                'status_detail' => null,
                'id' => null,
                'payment' => $payment
            );
        }
    }

    public static function payment ($data, $user, $order)
    {
        MercadoPago\SDK::setAccessToken(self::access_token);

        if (array_key_exists('holderName', $data)){
            $name = explode(' ', $data['holderName']);
        }else{
            $name = explode(' ', $user->NAME);
        }

        $items = [];

        $account = $order->USER_ACCOUNT_ID ? $order->USER_ACCOUNT_ID : 'NULL';

        $productList = DB::select("CALL SP_GET_VS_ORDER_ITEM_LIST_EXTERNAL({$order->USER_ID}, {$account}, {$order->ID})");

        foreach ($productList as $item){
            array_push($items, [
                "id" => $item->VS_PRODUCT_ID,
                "title" => $item->PRODUCT_NAME,
                "description" => $item->PRODUCT,
                "category_id" => $item->VS_CATEGORY,
                "quantity" => $item->UNITS,
                "unit_price" => (double) $item->SALE_PRICE_DISCOUNT
            ]);
        }

        $customer = MercadoPagoCustomer::where('DOCUMENT', $data['payer']['identification']['number'])->first();

        if ($customer){
           $customer = MercadoPago\Customer::find_by_id($customer->CUSTOMER_ID);
        }

        if (empty($customer)){
            $customers = MercadoPago\Customer::search(['email' => $data['payer']['email']]);
            if (count($customers) > 0){
                $customer = $customers[0];
            }
            if (!empty($customer) && property_exists($customer, 'id')){
                $name = [
                    $customer->first_name,
                    $customer->last_name,
                ];
                if (! MercadoPagoCustomer::where('DOCUMENT', $customer->identification->number)->first()){
                    MercadoPagoCustomer::create([
                        'NAME' => implode(' ', $name),
                        'TYPE_DOCUMENT_ID' => $customer->identification->type == 'CPF' ? 1 : 2,
                        'DOCUMENT' => $customer->identification->number,
                        'CUSTOMER_ID' => $customer->id
                    ]);
                }
            }
        }

        if (empty($customer)){
            $customer = new MercadoPago\Customer();
            $customer->identification = [
                'type' => $data['payer']['identification']['type'],
                'number' => $data['payer']['identification']['number']
            ];
            $customer->first_name = $name[0];
            $customer->last_name = $name[count($name) - 1];
            $customer->email = $data['payer']['email'];
            $customer->save();

            if (!empty($customer)){
                if ($customer->id){
                    MercadoPagoCustomer::create([
                        'NAME' => implode(' ', $name),
                        'TYPE_DOCUMENT_ID' => $data['payer']['identification']['type'] == 'CPF' ? 1 : 2,
                        'DOCUMENT' => $data['payer']['identification']['number'],
                        'CUSTOMER_ID' => $customer->id
                    ]);
                }
            }
        }

        if (property_exists($customer, 'id') && !is_null($data['token'])) {
            $card = new MercadoPago\Card();
            $card->token = $data['token'];
            $card->customer_id = $customer->id;
            $card->save();
        }

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = (float)$data['transaction_amount'];

        $payment->token = $data['token'];
        $payment->description = $data['description'];
        $payment->installments = (int)$data['installments'];
        $payment->payment_method_id = $data['payment_method_id'];
        $payment->issuer_id = (int)$data['issuer_id'];
        $payment->external_reference = $data['VS_ORDER_ID'];
        $payment->additional_info = array(
            "items" => $items,
            "payer" => array(
                "first_name" => $name[0],
                "last_name" => $name[count($name) - 1],
                "phone" => array(
                    "area_code" => (string) str_replace('+', '', $user->DDI),
                    "number" => (string) str_replace([' ', '-'], ['', ''], $user->PHONE)
                ),
                "address" => array(
                    "street_name" => (string)$user->ADDRESS,
                    "street_number" => (string)$user->NUMBER,
                    "zip_code" => (string)$user->ZIP_CODE
                )
            )
        );

        if (! empty($customer)){
            if ($customer->id){
                $payment->payer = [
                    'type' => 'customer',
                    'id' => $customer->id
                ];
            }
        }else{
            $payer = new MercadoPago\Payer();
            $payer->first_name = $name[0];
            $payer->last_name = $name[count($name) - 1];
            $payer->email = $data['payer']['email'];
            $payer->identification = array(
                "type" => $data['payer']['identification']['type'],
                "number" => $data['payer']['identification']['number']
            );
            $payment->payer = $payer;
        }

        $payment->save();

        return array(
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'id' => $payment->id,
            'payment' => $payment
        );
    }
}
