<?php

namespace App\Http\Controllers;

use App\Mail\CompraBoletoMail;
use App\Mail\RegistrationRequestMail;
use App\Models\Adm;
use App\Models\Boleto;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SendWhatsapp;
use App\Models\User;
use App\Models\UserAccount;
use App\Utils\HtmlWriter;
use App\Utils\Invoice;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class BoletoController extends Controller
{
    private $boleto;

    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_ORDER_ITEM('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_ORDER_ITEM');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }

    }

    public function boletoVs($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $request['PAYMENT_METHOD_ID'] = "1";

            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_VS_ORDER('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");

            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_VS_ORDER');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function show($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'id' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByUser($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $response = Http::get("https://prd-api.u4cdev.com/boleto/get-billet/{$request->id}");
            if($response->status() >= 200 && $response->status() < 300){
                $data = json_decode($response->body());
                return (new Message())->defaultMessage(1, 200, $data);
            }else{
                return response()->json(['ERROR' => ["MESSAGE" => "ERROR WHEN FINDING BOLETOS"]], 400);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function cancel($id, Request $request)
    {

        Validator::make($request->all(), [
            'digitableLine' => 'required'
        ])->validate();

        $user = UserAccount::find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $order = OrderItem::where('BILLET_DIGITABLE_LINE', $request->digitableLine)->first();

            if($order){
                $json = MassiveJsonConverter::generateJson('INSERT', $order->ID, 1, 1, $request->NOTE);
                if($order->STATUS_ORDER_ID != 1){
                    return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER CANNOT BE CHANGED"]], 403);
                }

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Accept' => 'application/json',
                    'Authorization' => $this->boleto->getAuthorization(),
                    'partner' => $this->boleto->getPartner()
                ])->post("https://prd-api.u4cdev.com/boleto/cancel/{$request->digitableLine}");
                if($response->status() >= 200 && $response->status() < 300 && $response->body() == ""){

                    $account = UserAccount::find($order->USER_ACCOUNT_ID);
                    $user = User::find($account->USER_ID);

                    $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->boletoCancel($order->BILLET_DIGITABLE_LINE, $order->ID, $order->BILLET_NET_PRICE);
                    $mg = new MailGunFactory();

                    $email = explode('@', $user->EMAIL);
                    if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                        $mail = Mail::to($user->EMAIL)->send(new CompraBoletoMail($html));
                        $mail = true;
                    }else{
                        $mail = $mg->send($user->EMAIL, 'Cancelamento de boleto', $html);
                    }

                    DB::select("CALL SP_NEW_BILLET_LOG_JSON('{$json}')");
                    DB::select("UPDATE ORDER_ITEM SET BILLET_ID = NULL, BILLET_DIGITABLE_LINE = NULL, BILLET_URL_PDF = NULL, BILLET_NET_PRICE = NULL, BILLET_DATE = NULL, BILLET_FEE = 0, PAYMENT_METHOD_ID = NULL WHERE ID = {$order->ID}");

                    if($mail){
                        return (new Message())->defaultMessage(1, 200);
                    }else{
                        return (new Message())->defaultMessage(20, 500);
                    }
                }else{
                    $data = json_decode($response->body());
                    return response()->json(['ERROR' => ["MESSAGE" => $data->message]], $data->statusCode);
                }
            }else{
                return (new Message())->defaultMessage(17, 404);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function generateVoucher(Request $request)
    {
        Validator::make($request->all(), [
            'id' => 'required'
        ])->validate();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
            'Authorization' => $this->boleto->getAuthorization(),
            'partner' => $this->boleto->getPartner()
        ])->get("https://prd-api.u4cdev.com/boleto/generate-voucher/{$request->id}");

        if($response->status() >= 200 && $response->status() < 300){
            $data = json_decode($response->body());
            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return response()->json(['ERROR' => ["MESSAGE" => "THERE WAS AN ERROR WHEN GENERATING VOUCHER"]], 400);
        }
    }

    public function create(Request $request)
    {
        Validator::make($request->all(), [
            'ORDER_ITEM_ID' => 'required',
            'P_DIGITAL_PLATFORM_ID' => 'required'
        ])->validate();

        $order = OrderItem::find($request->ORDER_ITEM_ID);
        if($order){

            if($order->STATUS_ORDER_ID != 1){
                return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER CANNOT BE CHANGED"]], 403);
            }

            if($order->BILLET_ID != null || $order->BILLET_ID != ''){
                return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER ALREADY HAS A LINKED BOLETO"]], 400);
            }

            $account = UserAccount::find($order->USER_ACCOUNT_ID);
            if($account){
                $user = User::find($account->USER_ID);

                if($user){

                    if ((new JwtValidation())->validateByUser($user->ID, $request) == false) {
                        return (new Message())->defaultMessage(41, 403);
                    }

                    if(strlen($user->STATE) > 2){
                        return response()->json(["ERROR" => ["MESSAGE" => "THE STATE FIELD OF YOUR REGISTRATION IS INVALID, ONLY 2 CHARACTERS ARE ACCEPTED"]], 400);
                    }

                    DB::select("UPDATE ORDER_ITEM SET PAYMENT_METHOD_ID = 1 WHERE ID = {$order->ID}");

                    if($request->P_DIGITAL_PLATFORM_ID == 1){
                        $type = "billing";
                    }else{
                        $type = "deposit";
                    }

                    $data = (DB::select("
                    SELECT FN_GET_DAYS_FOR_PAYMENT_DUE() as due,
                            FN_GET_BILLET_FEE() as tax,
                             FN_GET_QUOTE() AS quote
                    "))[0];
                    $today = date('Y-m-d');
                    $due = date('Y-m-d', strtotime($today . " + ".$data->due." day"));

                    $amount = ($order->NET_PRICE*$data->quote)+$data->tax;
                    $amount = number_format((float)$amount, 2, '.', '');

                    $content = [
                        "message" => "NICKNAME: " . $account->NICKNAME,
                        "amount" => (float)$amount,
                        "due_date" => $due,
                        "type" => $type
                    ];

                    if($user->TYPE_DOCUMENT_ID == 1 ||$user->TYPE_DOCUMENT_ID == 3){
                        $name = $user->NAME;
                    }else{
                        $name = $user->SOCIAL_REASON;
                    }

                    if($type == 'billing'){
                        $content += [
                            "payer" => [
                                "documentNumber" => $user->DOCUMENT,
                                "name" => $name,
                                "street_address" => $user->ADDRESS,
                                "number" => "{$user->NUMBER}",
                                "neighborhood" => $user->NEIGHBORHOOD,
                                "cep" => $user->ZIP_CODE,
                                "city" => $user->CITY,
                                "state" => $user->STATE,
                                "saveContact" => true
                            ],
                            "externalId" => "$order->ID"
                        ];
                    }

                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Accept' => 'application/json',
                        'Authorization' => $this->boleto->getAuthorization(),
                        'partner' => $this->boleto->getPartner()
                    ])->post("https://prd-api.u4cdev.com/boleto/create",
                        $content
                    );

                    if($response->status() >= 200 || $response->status() < 300){

                        $data = json_decode($response->body());
                        if(property_exists($data, 'statusCode')){
                            return response()->json(['ERROR' => ["MESSAGE" => $data->message]], $data->statusCode);
                        }else{
                            $registerBillet = DB::select("CALL SP_REGISTER_BILLET({$account->ID}, {$order->ID}, '{$data->id}', '{$data->digitableLine}', '{$data->billet}', '{$data->amount}', {$request->P_DIGITAL_PLATFORM_ID})");
                            if($registerBillet[0]->CODE == 1){
                                $json = MassiveJsonConverter::generateJson('INSERT', $order->ID, 1, 0);
                                DB::select("CALL SP_NEW_BILLET_LOG_JSON('{$json}')");
                                $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->boletoSend($data->digitableLine, $data->billet, $data->amount);
                                $mg = new MailGunFactory();

                                $email = explode('@', $user->EMAIL);
                                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                                    $mail = Mail::to($user->EMAIL)->send(new CompraBoletoMail($html));
                                    $mail = true;
                                }else{
                                    $mail = $mg->send($user->EMAIL, 'Nova compra realizada', $html);
                                }

                                if($mail){
                                    return (new Message())->defaultMessage(1, 200, $data);
                                }else{
                                    return (new Message())->defaultMessage(20, 500);
                                }
                            }else{
                                return (new Message())->defaultMessage($registerBillet[0]->CODE, 400);
                            }
                        }
                    }else{
                        return response()->json(['ERROR' => ["MESSAGE" => "ERROR WHEN GENERATING BOLETO"]], 400);
                    }
                }else{
                    return (new Message())->defaultMessage(18, 404);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function externalBillet(Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required',
            'P_DIGITAL_PLATFORM_ID' => 'required'
        ])->validate();

        $order = \App\Models\VS\Order::find($request->VS_ORDER_ID);
        if(!$order) return response()->json(['ERROR' => ["MESSAGE" => "ORDER ITEM NOT FOUND"]], 404);
        if($order->STATUS_ORDER_ID != 1) return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER CANNOT BE CHANGED"]], 403);
        if($order->BILLET_ID != null || $order->BILLET_ID != '') return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER ALREADY HAS A LINKED BOLETO"]], 400);

        $user = User::find($order->USER_ID);
        if(!$user) return (new Message())->defaultMessage(18, 404);
        if ((new JwtValidation())->validateByUser($user->ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        $type = "deposit";
        if($request->P_DIGITAL_PLATFORM_ID == 1) $type = "billing";


        $data = (DB::select("
                    SELECT FN_GET_DAYS_FOR_PAYMENT_DUE() as due,
                            FN_GET_BILLET_FEE() as tax
                    "))[0];
        $today = date('Y-m-d');
        $due = date('Y-m-d', strtotime($today . " + ".$data->due." day"));

        $amount = $order->NET_PRICE+$data->tax;
        $amount = number_format((float)$amount, 2, '.', '');

        $content = [
            "message" => "White Club",
            "amount" => (float)$amount,
            "due_date" => $due,
            "type" => $type
        ];

        if($user->TYPE_DOCUMENT_ID == 1 ||$user->TYPE_DOCUMENT_ID == 3){
            $name = $user->NAME;
        }else{
            $name = $user->SOCIAL_REASON;
        }

        if($type == 'billing'){
            $content += [
                "payer" => [
                    "documentNumber" => $user->DOCUMENT,
                    "name" => $name,
                    "street_address" => $order->ADDRESS,
                    "number" => "{$order->NUMBER}",
                    "neighborhood" => $order->NEIGHBORHOOD,
                    "cep" => $order->ZIP_CODE,
                    "city" => $order->CITY,
                    "state" => $order->STATE,
                    "saveContact" => true
                ],
                "externalId" => "$order->ID"
            ];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Accept' => 'application/json',
            'Authorization' => $this->boleto->getAuthorization(),
            'partner' => $this->boleto->getPartner()
        ])->post("https://prd-api.u4cdev.com/boleto/create",
            $content
        );

        if($response->status() >= 200 || $response->status() < 300){

            $data = json_decode($response->body());
            if(property_exists($data, 'statusCode')) return response()->json(['ERROR' => ["MESSAGE" => $data->message]], $data->statusCode);

            //$account = null;
            //if($order->USER_ACCOUNT_ID != null) $account = $order->USER_ACCOUNT_ID;
            //dd("CALL SP_VS_REGISTER_BILLET_EXTERNAL({$account}, {$order->ID}, '{$data->id}', '{$data->digitableLine}', '{$data->billet}', '{$data->amount}', {$request->P_DIGITAL_PLATFORM_ID})");
            $registerBillet = DB::select("CALL SP_VS_REGISTER_BILLET_EXTERNAL({$user->ID}, {$order->ID}, '{$data->id}', '{$data->digitableLine}', '{$data->billet}', '{$data->amount}', {$request->P_DIGITAL_PLATFORM_ID})");
            if($registerBillet[0]->CODE != 1) return (new Message())->defaultMessage($registerBillet[0]->CODE, 400);
            $json = MassiveJsonConverter::generateJson('INSERT', $order->ID, 2, 0);
            DB::select("CALL SP_NEW_BILLET_LOG_JSON('{$json}')");
            $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->boletoSend($data->digitableLine, $data->billet, $data->amount);
            $mg = new MailGunFactory();

            $email = explode('@', $user->EMAIL);
            if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                $mail = Mail::to($user->EMAIL)->send(new CompraBoletoMail($html));
                $mail = true;
            }else{
                $mail = $mg->send($user->EMAIL, 'Nova compra realizada', $html);
            }

            if(!$mail)  return (new Message())->defaultMessage(20, 400);

            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return response()->json(['ERROR' => ["MESSAGE" => "ERROR WHEN GENERATING BOLETO"]], 400);
        }
    }

    public function storeSchool(Request $request)
    {
        Validator::make($request->all(), [
            'ORDER_ID' => 'required',
            'P_DIGITAL_PLATFORM_ID' => 'required'
        ])->validate();

        $order = \App\Models\School\Order::find($request->ORDER_ID);
        if($order){
            dd($order);
            if($order->STATUS_ORDER_ID != 1){
                return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER CANNOT BE CHANGED"]], 403);
            }

            if($order->BILLET_ID != null || $order->BILLET_ID != ''){
                return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER ALREADY HAS A LINKED BOLETO"]], 400);
            }

            $account = UserAccount::find($order->USER_ACCOUNT_ID);
            if($account){
                $user = User::find($account->USER_ID);

                if($user){

                    if($request->P_DIGITAL_PLATFORM_ID == 1){
                        $type = "billing";
                    }else{
                        $type = "deposit";
                    }

                    $data = (DB::select("
                    SELECT FN_GET_DAYS_FOR_PAYMENT_DUE() as due,
                            FN_GET_BILLET_FEE() as tax
                    "))[0];
                    $today = date('Y-m-d');
                    $due = date('Y-m-d', strtotime($today . " + ".$data->due." day"));

                    $amount = $order->NET_PRICE+$data->tax;
                    $amount = number_format((float)$amount, 2, '.', '');

                    $content = [
                        "message" => "White Club",
                        "amount" => (float)$amount,
                        "due_date" => $due,
                        "type" => $type
                    ];

                    if($user->TYPE_DOCUMENT_ID == 1 ||$user->TYPE_DOCUMENT_ID == 3){
                        $name = $user->NAME;
                    }else{
                        $name = $user->SOCIAL_REASON;
                    }

                    if($type == 'billing'){
                        $content += [
                            "payer" => [
                                "documentNumber" => $user->DOCUMENT,
                                "name" => $name,
                                "street_address" => $order->ADDRESS,
                                "number" => "{$order->NUMBER}",
                                "neighborhood" => $order->NEIGHBORHOOD,
                                "cep" => $order->ZIP_CODE,
                                "city" => $order->CITY,
                                "state" => $order->STATE,
                                "saveContact" => true
                            ],
                            "externalId" => "$order->ID"
                        ];
                    }

                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Accept' => 'application/json',
                        'Authorization' => $this->boleto->getAuthorization(),
                        'partner' => $this->boleto->getPartner()
                    ])->post("https://prd-api.u4cdev.com/boleto/create",
                        $content
                    );

                    if($response->status() >= 200 || $response->status() < 300){

                        $data = json_decode($response->body());
                        if(property_exists($data, 'statusCode')){
                            return response()->json(['ERROR' => ["MESSAGE" => $data->message]], $data->statusCode);
                        }else{
                            /** O MIGUEL VAI CRIAR A SP **/
                            $registerBillet = DB::select("CALL SP_VS_REGISTER_BILLET({$account->ID}, {$order->ID}, '{$data->id}', '{$data->digitableLine}', '{$data->billet}', '{$data->amount}', {$request->P_DIGITAL_PLATFORM_ID})");
                            if($registerBillet[0]->CODE == 1){
                                $json = MassiveJsonConverter::generateJson('INSERT', $order->ID, 2, 0);
                                DB::select("CALL SP_NEW_BILLET_LOG_JSON('{$json}')");
                                $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->boletoSend($data->digitableLine, $data->billet, $data->amount);
                                $mg = new MailGunFactory();

                                $email = explode('@', $user->EMAIL);
                                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                                    $mail = Mail::to($user->EMAIL)->send(new CompraBoletoMail($html));
                                    $mail = true;
                                }else{
                                    $mail = $mg->send($user->EMAIL, 'Nova compra realizada', $html);
                                }

                                if($mail){
                                    return (new Message())->defaultMessage(1, 200, $data);
                                }else{
                                    return (new Message())->defaultMessage(20, 500);
                                }
                            }else{
                                return (new Message())->defaultMessage($registerBillet[0]->CODE, 400);
                            }
                        }
                    }else{
                        return response()->json(['ERROR' => ["MESSAGE" => "ERROR WHEN GENERATING BOLETO"]], 400);
                    }
                }else{
                    return (new Message())->defaultMessage(18, 404);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return response()->json(['ERROR' => ["MESSAGE" => "ORDER ITEM NOT FOUND"]], 404);
        }
    }

    //GERAÇÂO DE BOLETO DA VIRTUAL STORE
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required',
            'P_DIGITAL_PLATFORM_ID' => 'required'
        ])->validate();

        $order = \App\Models\VS\Order::find($request->VS_ORDER_ID);
        if($order){

            if($order->STATUS_ORDER_ID != 1){
                return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER CANNOT BE CHANGED"]], 403);
            }

            if($order->BILLET_ID != null || $order->BILLET_ID != ''){
                return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER ALREADY HAS A LINKED BOLETO"]], 400);
            }

            $account = UserAccount::find($order->USER_ACCOUNT_ID);
            if($account){
                $user = User::find($account->USER_ID);

                if($user){

                    if ((new JwtValidation())->validateByUser($user->ID, $request) == false) {
                        return (new Message())->defaultMessage(41, 403);
                    }

                    if($request->P_DIGITAL_PLATFORM_ID == 1){
                        $type = "billing";
                    }else{
                        $type = "deposit";
                    }

                    $data = (DB::select("
                    SELECT FN_GET_DAYS_FOR_PAYMENT_DUE() as due,
                            FN_GET_BILLET_FEE() as tax
                    "))[0];
                    $today = date('Y-m-d');
                    $due = date('Y-m-d', strtotime($today . " + ".$data->due." day"));

                    $amount = $order->NET_PRICE+$data->tax;
                    $amount = number_format((float)$amount, 2, '.', '');

                    $content = [
                        "message" => "White Club",
                        "amount" => (float)$amount,
                        "due_date" => $due,
                        "type" => $type
                    ];

                    if($user->TYPE_DOCUMENT_ID == 1 ||$user->TYPE_DOCUMENT_ID == 3){
                        $name = $user->NAME;
                    }else{
                        $name = $user->SOCIAL_REASON;
                    }

                    if($type == 'billing'){
                        $content += [
                            "payer" => [
                                "documentNumber" => $user->DOCUMENT,
                                "name" => $name,
                                "street_address" => $order->ADDRESS,
                                "number" => "{$order->NUMBER}",
                                "neighborhood" => $order->NEIGHBORHOOD,
                                "cep" => $order->ZIP_CODE,
                                "city" => $order->CITY,
                                "state" => $order->STATE,
                                "saveContact" => true
                            ],
                            "externalId" => "$order->ID"
                        ];
                    }

                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Accept' => 'application/json',
                        'Authorization' => $this->boleto->getAuthorization(),
                        'partner' => $this->boleto->getPartner()
                    ])->post("https://prd-api.u4cdev.com/boleto/create",
                        $content
                    );

                    if($response->status() >= 200 || $response->status() < 300){

                        $data = json_decode($response->body());
                        if(property_exists($data, 'statusCode')){
                            return response()->json(['ERROR' => ["MESSAGE" => $data->message]], $data->statusCode);
                        }else{
                            $registerBillet = DB::select("CALL SP_VS_REGISTER_BILLET({$account->ID}, {$order->ID}, '{$data->id}', '{$data->digitableLine}', '{$data->billet}', '{$data->amount}', {$request->P_DIGITAL_PLATFORM_ID})");
                            if($registerBillet[0]->CODE == 1){
                                $json = MassiveJsonConverter::generateJson('INSERT', $order->ID, 2, 0);
                                DB::select("CALL SP_NEW_BILLET_LOG_JSON('{$json}')");
                                $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->boletoSend($data->digitableLine, $data->billet, $data->amount);
                                $mg = new MailGunFactory();

                                $email = explode('@', $user->EMAIL);
                                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                                    $mail = Mail::to($user->EMAIL)->send(new CompraBoletoMail($html));
                                    $mail = true;
                                }else{
                                    $mail = $mg->send($user->EMAIL, 'Nova compra realizada', $html);
                                }

                                if($mail){
                                    return (new Message())->defaultMessage(1, 200, $data);
                                }else{
                                    return (new Message())->defaultMessage(20, 500);
                                }
                            }else{
                                return (new Message())->defaultMessage($registerBillet[0]->CODE, 400);
                            }
                        }
                    }else{
                        return response()->json(['ERROR' => ["MESSAGE" => "ERROR WHEN GENERATING BOLETO"]], 400);
                    }
                }else{
                    return (new Message())->defaultMessage(18, 404);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return response()->json(['ERROR' => ["MESSAGE" => "ORDER ITEM NOT FOUND"]], 404);
        }
    }

    public function callbackU4C(Request $request)
    {
        //token: E2FE9EB29A47F4FCF5B37D3F9D8D872EF43372FEBE2CD2
        if(!$request->hasHeader('token') ||
            strlen($request->header('token')) != 46 ||
            $request->header('token') != env("CALLBACK_TOKEN")){
            return response()->json(['ERROR' => ["MESSAGE" => "ACCESS DENIED"]], 403);
        }

        Validator::make($request->all(),[
            'id' => 'required',
            'externalId' => 'required'
        ])->validate();

        $result = DB::select("CALL SP_APPROVE_AUTOMATIC_PAYMENT('{$request->id}', 1, '77de68daecd823babbb58edb1c8e14d7106e83bb')");
        if($result[0]->CODE == 1){

            DB::select("CALL SP_RECORD_BILLET_PAYMENT_LOG(1, '{$request->id}', {$result[0]->CODE})");

            $vsOrder = \App\Models\VS\Order::where('BILLET_ID', $request->id)->first();
            $order = OrderItem::where('BILLET_ID', $request->id)->first();
            if($order){
                $account = UserAccount::find($order->USER_ACCOUNT_ID);
                if($account){
                    $user = User::find($account->USER_ID);
                    if($user){
                        if($user->TYPE_DOCUMENT_ID == 1 || $user->TYPE_DOCUMENT_ID == 3){
                            $name =  $user->NAME;
                        }else{
                            $name =  $user->SOCIAL_REASON;
                        }
                        if ($order->PRODUCT_ID == 12){
                            $response = Http::post(env('VG_SCHOOL')."/api/register-for-api", [
                                'first_name' => '',
                                'last_name' => $name,
                                'email' => $user->EMAIL,
                                'password' => $user->ID.$user->EMAIL,
                                'flag' => 1.5,
                                'phone' => $user->DDI. ' ' . $user->PHONE,
                                'address' => $user->ADDRESS,
                                'city' => $user->CITY,
                                'state' => $user->STATE,
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
                                'email' => $user->EMAIL,
                                'password' => $user->ID.$user->EMAIL,
                                'flag' => $order->PRODUCT_ID,
                                'phone' => $user->DDI. ' ' . $user->PHONE,
                                'address' => $user->ADDRESS,
                                'city' => $user->CITY,
                                'state' => $user->STATE,
                            ]);
                        }

                        if($response->status() >= 200 && $response->status() < 300){
                            Invoice::generateOne($order->ID, true, true);
                            try {

                                $data = [
                                    'WHATSAPP' => $user->DDI . $user->PHONE,
                                    'NAME' => $user->NAME,
                                    'USER_ID' => $user->ID,
                                    'USER_ACCOUNT_ID' => $account->ID,
                                    'COMBO' => Product::getComboName($order->PRODUCT_ID)
                                ];

                                SendWhatsapp::sendMessage($data, SendWhatsapp::CONFIRM_COMBO);
                                return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                            }catch (\Exception $e){
                                return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                            }
                        }elseif (($response->status() == 403)){
                            Invoice::generateOne($order->ID, true, true);
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
                                    'USER_ACCOUNT_ID' => $account ? $account->ID : null
                                ];

                                SendWhatsapp::sendMessage($data, SendWhatsapp::CONFIRM_PRODUCT);
                                return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                            }catch (\Exception $e){
                                return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                            }
                        }else{
                            return response()->json(['ERROR' => ["THERE WAS AN ERROR"]], 400);
                        }
                    }else{
                        return (new Message())->defaultMessage(18, 404);
                    }
                }else {
                    return (new Message())->defaultMessage(13, 404);
                }
            }elseif ($vsOrder) {
                try {
                    $user = User::find($vsOrder->USER_ID);
                    $account = UserAccount::find($vsOrder->USER_ACCOUNT_ID);
                    if(!$user){
                        $account = UserAccount::find($vsOrder->USER_ACCOUNT_ID);
                        $user = $account->USER_ID;
                    }

                    $data = [
                        'WHATSAPP' => $user->DDI . $user->PHONE,
                        'NAME' => $user->NAME,
                        'USER_ID' => $user->ID,
                        'USER_ACCOUNT_ID' => $account ? $account->ID : null
                    ];

                    SendWhatsapp::sendMessage($data, SendWhatsapp::CONFIRM_PRODUCT);
                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                }catch (\Exception $e){
                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                }
            }else{
                return (new Message())->defaultMessage(1, 200);
            }
        }else{

            DB::select("CALL SP_RECORD_BILLET_PAYMENT_LOG(1, '{$request->id}', {$result[0]->CODE})");

            return (new Message())->defaultMessage($result[0]->CODE, 400);
        }
    }

    public function forceApprovePayment(Request $request)
    {
        //SOMENTE DURANTE OS TESTES

        Validator::make($request->all(), [
            'digitableLine' => 'required',
        ])->validate();

        $aprove = Http::withHeaders([
            'token' => '05da02fe-23f1-4198-af98-9b336f2d4ab3',
            'partner' => '71941c56-9dc6-4ac6-854c-6bc22307b0f9'
        ])->post("https://hml-api.u4cdev.com/boleto/force-callback", [
            "digitableline" => $request->digitableLine
        ]);
        if($aprove->status() >= 200 && $aprove->status() < 300){
            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => ["MESSAGE" => "ERROR"]], 400);
        }
    }
}
