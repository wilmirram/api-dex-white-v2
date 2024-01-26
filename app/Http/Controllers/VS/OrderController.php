<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Mail\CompraBoletoMail;
use App\Mail\ProductSendMail;
use App\Mail\WithDrawalMail;
use App\Models\Adm;
use App\Models\Boleto;
use App\Models\SendWhatsapp;
use App\Models\UserAccount;
use App\Models\VS\Correios;
use App\Models\VS\Delivery;
use App\Models\VS\Order;
use App\Models\VS\Product;
use App\Models\VS\ProductPrice;
use App\Models\VS\UserAddress;
use App\Models\User;
use App\Utils\FileHandler;
use App\Utils\HtmlWriter;
use App\Utils\Invoice;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use function GuzzleHttp\json_decode;

class OrderController extends Controller
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function store(Request $request)
    {

        $data = $request->all();

        Validator::make($data, [
            "USER_ACCOUNT_ID" => 'required',
            "DELIVERY_ADDRESS" => 'required',
            "ORDER_ITEMS" => 'required',
        ])->validate();

        /*
        if (array_key_exists('USER_ID', $data)){
            $user = User::find($data['USER_ID']);
        }else{
            $userAccount = UserAccount::find($data['USER_ACCOUNT_ID']);
            $user = User::find($userAccount->USER_ID);
        }

        if (strlen($user->STATE) == 2){
            $data = Correios::freeShipping($user, $data);
        }
        */

        if(array_key_exists('SHIPPING_PRICE', $data)) unset($data['SHIPPING_PRICE']);
        if($data['USER_ACCOUNT_ID'] == 0){
            if (!array_key_exists('USER_ID', $data)) return response()->json(['ERROR' => ["MESSAGE" => "USER_ID IS REQUIRED"]], 422);
            $data['USER_ACCOUNT_ID'] = 'NULL';
            $data['USER_ID'] = (string) $data['USER_ID'];
        }

        if(!array_key_exists('USER_ID', $data)){
            $userAccount = UserAccount::find($data['USER_ACCOUNT_ID']);
            $data['USER_ID'] = (string) $userAccount->USER_ID;
        }

        if($data['DELIVERY_ADDRESS'] == 0){
            if (array_key_exists('USER_ID', $data)){
                $user = User::find($data['USER_ID']);
            }else{
                $userAccount = UserAccount::find($data['USER_ACCOUNT_ID']);
                $user = User::find($userAccount->USER_ID);
            }
            $delivery = [
                "COUNTRY_ID" => $user->COUNTRY_ID,
                "ZIP_CODE" => $user->ZIP_CODE,
                "ADDRESS" => $user->ADDRESS,
                "NUMBER" => $user->NUMBER,
                "COMPLEMENT" => $user->COMPLEMENT,
                "NEIGHBORHOOD" => $user->NEIGHBORHOOD,
                "CITY" => $user->CITY,
                "STATE" => $user->STATE
            ];
        }else{
            $address = UserAddress::find($data['DELIVERY_ADDRESS']);
            if($address){
                $delivery = [
                    "COUNTRY_ID" => $address->COUNTRY_ID,
                    "ZIP_CODE" => $address->ZIP_CODE,
                    "ADDRESS" => $address->ADDRESS,
                    "NUMBER" => $address->NUMBER,
                    "COMPLEMENT" => $address->COMPLEMENT,
                    "NEIGHBORHOOD" => $address->NEIGHBORHOOD,
                    "CITY" => $address->CITY,
                    "STATE" => $address->STATE
                ];
            }else{
                $userAccount = UserAccount::find($data['USER_ACCOUNT_ID']);
                $user = User::find($userAccount->USER_ID);
                $delivery = [
                    "COUNTRY_ID" => $user->COUNTRY_ID,
                    "ZIP_CODE" => $user->ZIP_CODE,
                    "ADDRESS" => $user->ADDRESS,
                    "NUMBER" => $user->NUMBER,
                    "COMPLEMENT" => $user->COMPLEMENT,
                    "NEIGHBORHOOD" => $user->NEIGHBORHOOD,
                    "CITY" => $user->CITY,
                    "STATE" => $user->STATE
                ];
            }
        }
        $data['DELIVERY_ADDRESS'] = $delivery;

        $result = json_encode($data, JSON_UNESCAPED_UNICODE);
        $result = str_replace("'", "\'", $result);
   
        $sp = DB::select("CALL SP_NEW_VS_ORDER('{$result}')");
        if($sp[0]->CODE == 1){
            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage($sp[0]->CODE, 400, null, 'SP_NEW_VS_ORDER');
        }
    }

    public function getTransferExternal(Request $request)
    {
        Validator::make($request->all(),[
            'hash' => 'required',
            'order_item_id' => 'required',
            'user_account_id' => 'required',
            'user_id' => 'required',
            'digital_platform_id' => 'required'
        ])->validate();
        $order = $this->order->find($request->order_item_id);
        if($order){

            $account = 'NULL';
            if ($request->user_account_id != 0){
                $userAccount = UserAccount::find($request->user_account_id);
                if(!$userAccount) return (new Message())->defaultMessage(13, 404);
                $account = $userAccount->ID;
            }

            if($order->STATUS_ORDER_ID == 1){
                $existing_hash = (DB::select("SELECT FN_EXISTING_HASH(1, '{$request->hash}') as hash"))[0]->hash;
                if($existing_hash != 0){
                    DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$account},
                                {$request->user_id},
                                {$request->order_item_id},
                                56,
                                NULL,
                                NULL
                             )");
                    return (new Message())->defaultMessage(56, 400);
                }else{
                    $response = Http::get("https://prd-api.u4cdev.com/transfers/internal/get-transfer/{$request->hash}");
                    $result = json_decode($response->body());
                    if(property_exists($result, 'statusCode')){
                        if($result->statusCode == 500){
                            $code = 20;
                        }elseif ($result->statusCode == 404){
                            $code = 57;
                        }
                        DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$account},
                                {$request->user_id},
                                {$request->order_item_id},
                                {$code},
                                NULL,
                                NULL
                             )");
                        return response()->json(['ERROR' => ["MESSAGE" => $result->message]], 400);
                    }else{
                        $date = new \DateTime($result->date);
                        $date = $date->format('Y-m-d H:i:s');
                        $re = DB::select("CALL SP_VS_APPROVE_PAYMENT_DIGITAL_PLATFORM(
                                {$request->user_id},
                                {$request->order_item_id},
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                '{$result->id}',
                                '{$result->transferTo}',
                                '{$result->amount}',
                                '{$date}',
                                '77de68daecd823babbb58edb1c8e14d7106e83bb'
                            )");

                        if($re[0]->CODE == 1){
                            if ($request->user_account_id != 0){
                                $user = User::find($userAccount->USER_ID);
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
                                    Invoice::generateOne($request->order_item_id, true, true);
                                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                                }elseif (($response->status() == 403)){
                                    Invoice::generateOne($request->order_item_id, true, true);
                                    return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                                }else{

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
                                        return (new Message())->defaultMessage(1, 200, 'SP_VS_APPROVE_PAYMENT_DIGITAL_PLATFORM');
                                    }catch (\Exception $e){
                                        return (new Message())->defaultMessage(1, 200, 'SP_VS_APPROVE_PAYMENT_DIGITAL_PLATFORM');
                                    }
                                }
                            }else{
                                Invoice::generateOne($request->order_item_id, true, true);
                                return response()->json(['SUCCESS' => ["PAYMENT APPROVED"]], 200);
                            }
                        }else{
                            DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$account},
                                {$request->user_id},
                                {$request->order_item_id},
                                {$re[0]->CODE},
                                NULL,
                                NULL
                             )");
                            return (new Message())->defaultMessage($re[0]->CODE, 400, 'SP_VS_APPROVE_PAYMENT_DIGITAL_PLATFORM');
                        }
                    }
                }
            }else{
                return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER CAN'T BE CHANGED"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(14, 404);
        }
    }

    public function getTransfer(Request $request)
    {
        Validator::make($request->all(),[
            'hash' => 'required',
            'vs_order_id' => 'required',
            'user_account_id' => 'required',
            'digital_platform_id' => 'required'
        ])->validate();

        $order = $this->order->find($request->vs_order_id);
        if($order){

            $userAccount = UserAccount::find($request->user_account_id);
            if($userAccount){

                if((new JwtValidation())->validateByUserAccount($userAccount->ID, $request) == false){
                    return (new Message())->defaultMessage(41, 403);
                }

                if($order->STATUS_ORDER_ID == 1){

                    $existing_hash = (DB::select("SELECT FN_EXISTING_HASH(1, '{$request->hash}') as hash"))[0]->hash;
                    if($existing_hash != 0){
                        DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                56,
                                NULL,
                                NULL
                             )");
                        return (new Message())->defaultMessage(56, 400);
                    }else{
                        $response = Http::get("https://prd-api.u4cdev.com/transfers/internal/get-transfer/{$request->hash}");
                        $result = json_decode($response->body());
                        if(property_exists($result, 'statusCode')){
                            if($result->statusCode == 500){
                                $code = 20;
                            }elseif ($result->statusCode == 404){
                                $code = 57;
                            }
                            DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                {$code},
                                NULL,
                                NULL
                             )");
                            return response()->json(['ERROR' => ["MESSAGE" => $result->message]], 400);
                        }else{
                            $date = new \DateTime($result->date);
                            $date = $date->format('Y-m-d H:i:s');
                            $re = DB::select("CALL SP_VS_APPROVE_PAYMENT_DIGITAL_PLATFORM(
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                '{$result->id}',
                                '{$result->transferTo}',
                                '{$result->amount}',
                                '{$date}',
                                '77de68daecd823babbb58edb1c8e14d7106e83bb'
                            )");
                            if($re[0]->CODE == 1){
                                return (new Message())->defaultMessage(1, 200);
                            }else{
                                DB::select("CALL SP_RECORD_VS_TRANSFER_PAYMENT_LOG(
                                {$request->digital_platform_id},
                                '{$request->hash}',
                                {$request->user_account_id},
                                {$request->vs_order_id},
                                {$re[0]->CODE},
                                NULL,
                                NULL
                             )");
                                return (new Message())->defaultMessage($re[0]->CODE, 400, null, 'SP_RECORD_VS_TRANSFER_PAYMENT_LOG');
                            }
                        }
                    }
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER CAN'T BE CHANGED"]], 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(14, 404);
        }
    }

    public function cancel($id, Request $request)
    {

        Validator::make($request->all(), [
            'digitableLine' => 'required'
        ])->validate();

            $order = Order::where('BILLET_DIGITABLE_LINE', $request->digitableLine)->first();

            if($order){
                $json = MassiveJsonConverter::generateJson('INSERT', $order->ID, 2, 1, $request->NOTE);
                if($order->STATUS_ORDER_ID != 1){
                    return response()->json(["ERROR" => ["MESSAGE" => "THIS ORDER CANNOT BE CHANGED"]], 403);
                }

                $boleto = new Boleto();

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Accept' => 'application/json',
                    'Authorization' => $boleto->getAuthorization(),
                    'partner' => $boleto->getPartner()
                ])->post("https://prd-api.u4cdev.com/boleto/cancel/{$request->digitableLine}");
                if($response->status() >= 200 && $response->status() < 300 && $response->body() == ""){
                    DB::select("CALL SP_NEW_BILLET_LOG_JSON('{$json}')");
                    DB::select("UPDATE VS_ORDER SET BILLET_ID = NULL, BILLET_DIGITABLE_LINE = NULL, BILLET_URL_PDF = NULL, BILLET_NET_PRICE = NULL, BILLET_DATE = NULL, BILLET_FEE = 0, PAYMENT_METHOD_ID = NULL WHERE ID = {$order->ID}");
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    $data = json_decode($response->body());
                    return response()->json(['ERROR' => ["MESSAGE" => $data->message]], $data->statusCode);
                }
            }else{
                return (new Message())->defaultMessage(17, 404);
            }
    }

    public function show($id, Request $request)
    {
        $user = UserAccount::find($id);
        if(!$user) $user = UserAccount::where('NICKNAME', $id)->first();

        if($user){

            /*if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }*/

            $orders = DB::select("CALL SP_GET_VS_ORDER_LIST({$user->ID})");
            return (new Message())->defaultMessage(1, 200, $orders);
        }else{
            return response()->json(['ERROR' => ['MESSAGE' => 'USER ACCOUNT NOT FOUND']], 404);
        }
    }

    public function getOrderItemList(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ACCOUNT_ID' => 'required',
            'VS_ORDER_ID' => 'required'
        ])->validate();

        $user = UserAccount::find($request->USER_ACCOUNT_ID);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $order = $this->order->find($request->VS_ORDER_ID);
            if($order){
                $order = DB::select("CALL SP_GET_VS_ORDER_ITEM_LIST({$request->USER_ACCOUNT_ID}, {$request->VS_ORDER_ID})");
                return (new Message())->defaultMessage(1, 200, $order);
            }else{
                return (new Message())->defaultMessage(14, 404);
            }
        }else{
            return response()->json(['ERROR' => ['MESSAGE' => 'USER ACCOUNT NOT FOUND']], 404);
        }
    }

    public function cancelOrder(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ACCOUNT_ID' => 'required',
            'VS_ORDER_ID' => 'required'
        ])->validate();

        $order = $this->order->find($request->VS_ORDER_ID);
        if(!$order) return (new Message())->defaultMessage(14, 404);

        $userAccountID = 'NULL';
        if ($request->USER_ACCOUNT_ID != 0){
            $account = UserAccount::find($request->USER_ACCOUNT_ID);
            if(!$account)   return response()->json(['ERROR' => ['MESSAGE' => 'USER ACCOUNT NOT FOUND']], 404);
            $userAccountID = $account->ID;
        }

        //if((new JwtValidation())->validateByUser($account->ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        if($order->STATUS_ORDER_ID != 1) return response()->json(['ERROR' => ['MESSAGE' => 'YOU CAN NOT CHANGE THE DATA OF THIS ORDER']], 400);

        $order = DB::select("CALL SP_DEL_VS_ORDER_OPEN({$order->USER_ID}, {$userAccountID}, {$request->VS_ORDER_ID})");
        if($order[0]->CODE != 1) return (new Message())->defaultMessage($order[0]->CODE, 400, null, 'SP_DEL_VS_ORDER_OPEN');
        return (new Message())->defaultMessage(1, 200);
    }

    public function addPaymentVoucher($id, Request $request)
    {
        Validator::make($request->all(), [
            'PAYMENT_VOUCHER' => 'required',
        ])->validate();

        $order = $this->order->find($id);
        if($order){
            if($order->STATUS_ORDER_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'YOU CAN NOT CHANGE THE DATA OF THIS ORDER']], 400);
            }
            if($order->PAYMENT_VOUCHER != NULL){
                return response()->json(['ERROR' => ['MESSAGE' => 'THIS ORDER JUST HAVE A VOUCHER']], 400);
            }
            $file = (new FileHandler())->writeFile($request->PAYMENT_VOUCHER, 'vs_voucher', $order->ID);
            DB::select("UPDATE VS_ORDER SET PAYMENT_VOUCHER = '{$file}', DT_PAYMENT_VOUCHER = NOW() WHERE id = {$id}");
            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getPaymentVoucher($id)
    {
        $order = $this->order->find($id);
        if($order){
            if($order->PAYMENT_VOUCHER === null || $order->PAYMENT_VOUCHER === ''){
                return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER DON'T HAVE A VOUCHER"]], 400);
            }else{
                $file = (new FileHandler())->getFile($order->PAYMENT_VOUCHER);
                $voucher = explode('.', $order->PAYMENT_VOUCHER);
                $voucherLink = ['Name' => $voucher[0],
                    'Ext' => $voucher[1],
                    'Data' => $file];
                return (new Message())->defaultMessage(1, 200, $voucherLink);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function removePaymentVoucher($id)
    {
        $order = $this->order->find($id);
        if($order){

            if($order->STATUS_ORDER_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'YOU CAN NOT CHANGE THE DATA OF THIS ORDER']], 400);
            }

            if($order->PAYMENT_VOUCHER == NULL || $order->PAYMENT_VOUCHER == ''){
                return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER DON'T HAVE A VOUCHER"]], 400);
            }else{
                $voucher = $order->PAYMENT_VOUCHER;

                if((new FileHandler())->removeFile($voucher) == true){
                    DB::select("UPDATE VS_ORDER SET PAYMENT_VOUCHER = NULL, DT_PAYMENT_VOUCHER = NULL, PAYMENT_METHOD_ID = NULL WHERE ID = {$id}");
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "ERROR OCCURRED WHEN REMOVING THE IMAGE"]], 400);
                }
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function admPaymentReport($uuid, Request $request)
    {

        Validator::make($request->all(), [
            'P_DT_PAYMENT_START' => 'required',
            'P_DT_PAYMENT_END' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_REPORT_VS_PAYMENT_ORDER('{$request->P_DT_PAYMENT_START}', '{$request->P_DT_PAYMENT_END}')");

            return (new Message())->defaultMessage(1, 200, $result);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function admPaymentReportXls($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'P_DT_PAYMENT_START' => 'required',
            'P_DT_PAYMENT_END' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_REPORT_VS_PAYMENT_ORDER('{$request->P_DT_PAYMENT_START}', '{$request->P_DT_PAYMENT_END}')");

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A'.'1', "ID");
            $sheet->setCellValue('B'.'1', 'NICKNAME');
            $sheet->setCellValue('C'.'1', "NOME");
            $sheet->setCellValue('D'.'1', "TIPO DE DOCUMENTO");
            $sheet->setCellValue('E'.'1', "DOCUMENTO");
            $sheet->setCellValue('F'.'1', "EMAIL");
            $sheet->setCellValue('G'.'1', "TELEFONE");
            $sheet->setCellValue('H'.'1', "COUNTRY");
            $sheet->setCellValue('I'.'1', "CODIGO POSTAL");
            $sheet->setCellValue('J'.'1', "ENDEREÇO");
            $sheet->setCellValue('K'.'1', "NUMERO");
            $sheet->setCellValue('L'.'1', "COMPLEMENTO");
            $sheet->setCellValue('M'.'1', "BAIRRO");
            $sheet->setCellValue('N'.'1', "CIDADE");
            $sheet->setCellValue('O'.'1', "ESTADO");
            $sheet->setCellValue('P'.'1', "CODIGO DE BARRAS");
            $sheet->setCellValue('Q'.'1', "NOME DO PRODUTO");
            $sheet->setCellValue('R'.'1', "DESCRIÇÃO DO PRODUTO");
            $sheet->setCellValue('S'.'1', "COR");
            $sheet->setCellValue('T'.'1', "TAMANHO");
            $sheet->setCellValue('U'.'1', "DATA DE PAGAMENTO");
            $sheet->setCellValue('V'.'1', "UNIDADES");
            $sheet->setCellValue('W'.'1', "PREÇO DE UNIDADE DE PRODUTO");
            $sheet->setCellValue('X'.'1', "PREÇO DE FABRICA");
            $sheet->setCellValue('Y'.'1', "PREÇO COM DESCONTO");
            $sheet->setCellValue('Z'.'1', "FRETE");
            $sheet->setCellValue('AA'.'1', "VALOR REAL FRETE");
            $sheet->setCellValue('AB'.'1', "MONTANTE BRUTO RECEBIDO");
            $sheet->setCellValue('AC'.'1', "VALOR RECEBIDO EM REAIS");
            $sheet->setCellValue('AD'.'1', "METODO DE PAGAMENTO");
            $sheet->setCellValue('AE'.'1', "DATA DE ENVIO");
            $sheet->setCellValue('AF'.'1', "CODIGO DE RASTREIO");
            $sheet->setCellValue('AG'.'1', "DATA ESTIMADA DE ENTREGA");

            for ($i = 0; $i < count($result); $i++){
                $color = '';
                $size = '';

                if($result[$i]->VARIATIONS_JSON != null){
                    $json = json_decode($result[$i]->VARIATIONS_JSON);
                    $color = property_exists($json, 'COR') ? $json->COR : '';
                    $size = property_exists($json, 'TAMANHO') ? $json->TAMANHO : '';
                }

                $realShippingCost = DB::select("SELECT SUM(REAL_SHIPPING_COST) as RSC FROM VS_DELIVERY WHERE VS_ORDER_ID = {$result[$i]->VS_ORDER_ID}");

                $sheet->setCellValue('A'.($i+2), $result[$i]->VS_ORDER_ID);
                $sheet->setCellValue('B'.($i+2), $result[$i]->NICKNAME);
                $sheet->setCellValue('C'.($i+2), $result[$i]->NAME);
                $sheet->setCellValue('D'.($i+2), $result[$i]->TYPE_DOCUMENT);
                $sheet->setCellValue('E'.($i+2), $result[$i]->DOCUMENT);
                $sheet->setCellValue('F'.($i+2), $result[$i]->EMAIL);
                $sheet->setCellValue('G'.($i+2), $result[$i]->PHONE);
                $sheet->setCellValue('H'.($i+2), $result[$i]->COUNTRY);
                $sheet->setCellValue('I'.($i+2), $result[$i]->ZIP_CODE);
                $sheet->setCellValue('J'.($i+2), $result[$i]->ADDRESS);
                $sheet->setCellValue('K'.($i+2), $result[$i]->NUMBER);
                $sheet->setCellValue('L'.($i+2), $result[$i]->COMPLEMENT);
                $sheet->setCellValue('M'.($i+2), $result[$i]->NEIGHBORHOOD);
                $sheet->setCellValue('N'.($i+2), $result[$i]->CITY);
                $sheet->setCellValue('O'.($i+2), $result[$i]->STATE);
                $sheet->setCellValue('P'.($i+2), $result[$i]->REFERENCE_CODE);
                $sheet->setCellValue('Q'.($i+2), $result[$i]->PRODUCT);
                $sheet->setCellValue('R'.($i+2), $result[$i]->PRODUCT_DESCRIPTION);
                $sheet->setCellValue('S'.($i+2), $color)
                                    ->getStyle('S'.($i+2))
                                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB(
                                        $color != '' && $color[0] == '#' ?
                                            str_replace('#', '', $color) :
                                            'FFFFFF');
                $sheet->setCellValue('T'.($i+2), $size);
                $sheet->setCellValue('U'.($i+2), $result[$i]->DT_PAYMENT);
                $sheet->setCellValue('V'.($i+2), $result[$i]->UNITS);
                $sheet->setCellValue('W'.($i+2), $result[$i]->PRODUCT_UNIT_PRICE);
                $sheet->setCellValue('X'.($i+2), $result[$i]->PRODUCT_FACTORY_PRICE);
                $sheet->setCellValue('Y'.($i+2), $result[$i]->SALE_PRICE_DISCOUNT);
                $sheet->setCellValue('Z'.($i+2), $result[$i]->SHIPPING_COST);
                $sheet->setCellValue('AA'.($i+2), $realShippingCost[0]->RSC);
                $sheet->setCellValue('AB'.($i+2), $result[$i]->GLOSS_AMOUNT_RECEIVED);
                $sheet->setCellValue('AC'.($i+2), $result[$i]->AMOUNT_RECEIVED_BRL);
                $sheet->setCellValue('AD'.($i+2), $result[$i]->PAYMENT_METHOD);
                $sheet->setCellValue('AE'.($i+2), $result[$i]->DT_SHIPPING);
                $sheet->setCellValue('AF'.($i+2), $result[$i]->TRACKING_CODE);
                $sheet->setCellValue('AG'.($i+2), $result[$i]->DT_ESTIMATED_DELIVERY);
            }

            $name = date('Y-m-d')."_VS-PAYMENT-REPORT";

            $writer = new Xlsx($spreadsheet);

            $writer->save('storage/exports/'.$name.'.xlsx');

            return response()->file("storage/exports/{$name}.xlsx", [ 'Content-Disposition' => "inline; filename={$name}.xlsx"]);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function search($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
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

    public function approveExternalOrder($uuid, Request $request)
    {
        Validator::make($request->all(),[
            'P_VS_ORDER_ID' => 'required',
            'P_PAYMENT_METHOD_ID' => 'required',
            'P_AMOUNT_RECEIVED' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm) return (new Message())->defaultMessage(27, 404);
        $order = $this->order->find($request->P_VS_ORDER_ID);
        if (!$order)return (new Message())->defaultMessage(17, 404);

        $result = DB::select("CALL SP_VS_APPROVE_PAYMENT_EXTERNAL(
                         '{$request->P_VS_ORDER_ID}',
                         '{$order->USER_ID}',
                         '{$request->P_PAYMENT_METHOD_ID}',
                         '{$request->P_AMOUNT_RECEIVED}',
                         '{$adm->UUID}'
                         )");

        if($result[0]->CODE != 1)  return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_VS_APPROVE_PAYMENT');

        $user = User::find($order->USER_ID);
        $account = UserAccount::find($order->USER_ACCOUNT_ID);
        if (!$user){
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

        return (new Message())->defaultMessage(1, 200);
    }

    public function approveVsOrder($uuid, Request $request)
    {
        Validator::make($request->all(),[
            'P_VS_ORDER_ID' => 'required',
            'NICKNAME' => 'required',
            'P_PAYMENT_METHOD_ID' => 'required',
            'P_AMOUNT_RECEIVED' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();
            if($userAccount){
                $token = [
                    'P_VS_ORDER_ID' => $request->P_VS_ORDER_ID,
                    'P_USER_ACCOUNT_ID' => $userAccount->ID,
                    'P_PAYMENT_METHOD_ID' => $request->P_PAYMENT_METHOD_ID,
                    'P_AMOUNT_RECEIVED' => $request->P_AMOUNT_RECEIVED,
                    'P_ADM_UUID' => $adm->UUID
                ];
                $token = json_encode($token);
                $token = base64_encode($token);
                $result = DB::select("CALL SP_VS_APPROVE_PAYMENT(
                         '{$request->P_VS_ORDER_ID}',
                         '{$userAccount->ID}',
                         '{$request->P_PAYMENT_METHOD_ID}',
                         '{$request->P_AMOUNT_RECEIVED}',
                         '{$token}',
                         '{$adm->UUID}'
                         )");
                if($result[0]->CODE == 1){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_VS_APPROVE_PAYMENT');
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function searchOrder($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $validFields = [
                'ID',
                'USER_ACCOUNT_ID',
                'DOCUMENT',
                'STATUS_ORDER_ID',
                'DT_REGISTER_START',
                'DT_REGISTER_END',
                'DELIVERY_STATUS_ID',
                'TRACKING_CODE',
                'DT_SHIPPING'
                ];
            MassiveJsonConverter::removeInvalidFields($validFields, $request);
            $json = (new MassiveJsonConverter())->generate("SEARCH", $request);
            $result = DB::select("CALL SP_SEARCH_VS_ORDER('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_VS_ORDER');
            }
        }else{
            return (new Message())->defaultMessage(27, 404, null, 'SP_SEARCH_VS_ORDER');
        }
    }

    public function confirmDelivery($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required',
            'TRACKING_CODE' => 'required',
            'DT_SHIPPING' => 'required'
        ])->validate();

        if($request->TRACKING_CODE == '' || $request->TRACKING_CODE == NULL || $request->TRACKING_CODE == 'NULL' || $request->TRACKING_CODE == null || $request->TRACKING_CODE == 'null'){
            return response()->json(['ERROR' => ['MESSAGE' => 'TRACKING_CODE CAN NOT BE EMPTY OR NULL']], 400);
        }

        if($request->DT_SHIPPING == '' || $request->DT_SHIPPING == NULL || $request->TRACKING_CODE == 'NULL' || $request->TRACKING_CODE == null || $request->TRACKING_CODE == 'null'){
            return response()->json(['ERROR' => ['MESSAGE' => 'DT_SHIPPING CAN NOT BE EMPTY OR NULL']], 400);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $request['DELIVERY_STATUS_ID'] = "2";
            $json = ((new MassiveJsonConverter())->generate("UPDATE", $request));
            $result = DB::select("CALL SP_UPDATE_VS_ORDER('{$json}', '{$uuid}', @P_CODE_LIST)");
            $select = DB::select("SELECT @P_CODE_LIST as code");
            if($select[0]->code == 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($select[0]->code, 400, null, 'SP_UPDATE_VS_ORDER');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getDeliveryList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $order = $this->order->find($request->VS_ORDER_ID);
            if ($order){
                $user = $order->USER_ID;
                if($user == null && $request->has('NICKNAME')){
                    $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();
                    $user = $userAccount->USER_ID;
                }
                $result = DB::select("CALL SP_GET_VS_DELIVERY_LIST({$order->ID}, {$user})");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(17, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getDeliveryProductList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required',
            'VS_DELIVERY_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm) return (new Message())->defaultMessage(27, 404);
        if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        $order = $this->order->find($request->VS_ORDER_ID);
        if(!$order) return (new Message())->defaultMessage(17, 404);
        if ($request->has('NICKNAME') && $request->NICKNAME != null){
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();
            $user = $userAccount->USER_ID;
        }else{
            $user = $order->USER_ID;
        }

        $delivery = Delivery::find($request->VS_DELIVERY_ID);
        if(!$delivery) return (new Message())->defaultMessage(17, 404);

        $result = DB::select("CALL SP_GET_DELIVERY_VS_ORDER_ITEM_LIST(
                                {$order->ID},
                                {$delivery->ID},
                                {$user})");

        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function updateTrackingCode($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'TRACKING_CODE' => 'required',
            'VS_DELIVERY_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm) return (new Message())->defaultMessage(27, 404);

        try {
            DB::select(
                "UPDATE VS_DELIVERY SET TRACKING_CODE = '{$request->TRACKING_CODE}',
                       ADM_ID = {$adm->ID} WHERE ID = {$request->VS_DELIVERY_ID}
                       ");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function updateDeliveryData($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'TRACKING_CODE' => 'required',
            'REAL_SHIPPING_COST' => 'required',
            'VS_DELIVERY_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(!$adm) return (new Message())->defaultMessage(27, 404);

        try {
            DB::select(
                 "UPDATE VS_DELIVERY SET TRACKING_CODE = '{$request->TRACKING_CODE}',
                       REAL_SHIPPING_COST = '{$request->REAL_SHIPPING_COST}',
                       ADM_ID = {$adm->ID} WHERE ID = {$request->VS_DELIVERY_ID}
                       ");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function sendAPackage($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required',
            'VS_DELIVERY_ID' => 'required',
            'TRACKING_CODE' => 'required',
            'DT_SHIPPING' => 'required',
            'REAL_SHIPPING_COST' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        if(!$adm) return (new Message())->defaultMessage(27, 404);
        $delivery = Delivery::find($request->VS_DELIVERY_ID);
        if ($delivery->DELIVERY_STATUS_ID == 2) return response()->json(['ERROR' => ['DATA' => 'PACKAGE ALREADY SENT']], 400);
        if(!$delivery) return (new Message())->defaultMessage(17, 404);
        $order = $this->order->find($request->VS_ORDER_ID);
        if(!$order) return (new Message())->defaultMessage(17, 404);
        if ($request->has('USER_ACCOUNT_ID')){
            $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
            $user = $userAccount->USER_ID;
        }else{
            $user = $order->USER_ID;
        }

        $user = User::find($user);
        if (!$user) return (new Message())->defaultMessage(17, 404);

        $prodList = '';

        $result = DB::select("CALL SP_GET_DELIVERY_VS_ORDER_ITEM_LIST(
                                {$order->ID},
                                {$delivery->ID},
                                {$user->ID})");
        foreach ($result as $product){
            $prodList .= "<li>$product->VS_PRODUCT</li>";
        }

        try {
            DB::select("UPDATE VS_DELIVERY SET
                            DELIVERY_STATUS_ID = 2,
                            DT_SHIPPING = '{$request->DT_SHIPPING}',
                            TRACKING_CODE = '{$request->TRACKING_CODE}',
                            REAL_SHIPPING_COST = '{$request->REAL_SHIPPING_COST}',
                            ADM_ID = {$adm->ID},
                            DT_LAST_UPDATE_ADM = NOW()
                        WHERE
                            VS_ORDER_ID = {$order->ID}
                        AND
                            ID = {$delivery->ID}
                    ");

            $packages = DB::select("SELECT ID, DELIVERY_STATUS_ID FROM VS_DELIVERY WHERE VS_ORDER_ID = {$order->ID}");
            $counter = count($packages);
            $sended = 0;
            foreach ($packages as $package){
                if ($package->DELIVERY_STATUS_ID == 2) $sended++;
            }
            if($sended == $counter){
                DB::select("UPDATE VS_ORDER SET
                                DELIVERY_STATUS_ID = 2
                            WHERE
                                ID = {$order->ID}
                    ");
            }else{
                DB::select("UPDATE VS_ORDER SET
                                DELIVERY_STATUS_ID = 5
                            WHERE
                                ID = {$order->ID}
                     ");
            }

            if ($request->has('CHIPS') && $request->CHIPS != 0) {
                foreach ($request->CHIPS as $chip) {
                    DB::select("UPDATE VS_ICCID_CHIP SET
                    VS_ORDER_ID = {$order->ID},
                    ADM_ID = {$adm->ID},
                    DT_LAST_UPDATE_ADM = NOW()
                WHERE NUMBER = '{$chip}'
                ");
                }
            }

        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }

        $mg = new MailGunFactory();

        $email = explode('@', $user->EMAIL);
        $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->productSended($request->TRACKING_CODE, $delivery->ID, $order->ID, $prodList);

        if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
            $mail = Mail::to($user->EMAIL)->send(new ProductSendMail($html));
        }else{
            $mail = $mg->send($user->EMAIL, 'Status de pedido atualizado!', $html);
        }
        $data = [
            'WHATSAPP' => $user->DDI . $user->PHONE,
            'NAME' => $user->NAME,
            'USER_ID' => $user->ID,
            'TRACKING_CODE' => $request->TRACKING_CODE,
            'USER_ACCOUNT_ID' => $request->has('USER_ACCOUNT_ID') ? $request->USER_ACCOUNT_ID : null
        ];

        SendWhatsapp::sendMessage($data, SendWhatsapp::TRACKING_CODE);

        return (new Message())->defaultMessage(1, 200);
    }

    public function getVsOrderList(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ID' => 'required',
            'P_USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $data = $request->all();

        if ($data['P_USER_ACCOUNT_ID'] == 0) $data['P_USER_ACCOUNT_ID'] = 'NULL';

        $result = DB::select("CALL SP_GET_VS_ORDER_LIST_EXTERNAL({$data['P_USER_ID']}, {$data['P_USER_ACCOUNT_ID']})");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function getOrderData(Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required'
        ])->validate();
        $result = [];

        $order = $this->order->find($request->VS_ORDER_ID);
        if (!$order) return (new Message())->defaultMessage(17, 400);

        if ($request->has('USER_ACCOUNT_ID')){
            $user = UserAccount::find($request->USER_ACCOUNT_ID);
            $id = $user->USER_ID;
        }
        elseif ($request->has('NICKNAME')){
            $user = UserAccount::where('NICKNAME', $request->NICKNAME)->first();
            $id = $user->USER_ID;
        }else{
            $id = $order->USER_ID;
        }
        $packages = DB::select("CALL SP_GET_VS_DELIVERY_LIST({$request->VS_ORDER_ID}, {$id})");

        foreach ($packages as $key => $package) {
            $productList = DB::select("CALL SP_GET_DELIVERY_VS_ORDER_ITEM_LIST(
                                {$request->VS_ORDER_ID},
                                {$package->VS_DELIVERY_ID},
                                {$id})");
            $package->PRODUCT_LIST = $productList;
            array_push($result, $package);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function confirmDeliveryToClient(Request $request)
    {
        Validator::make($request->all(), [
            'VS_DELIVERY_ID' => 'required',
            'VS_ORDER_ID' => 'required'
        ])->validate();

        $order = $this->order->find($request->VS_ORDER_ID);
        $delivery = Delivery::find($request->VS_DELIVERY_ID);
        if ($delivery->DELIVERY_STATUS_ID != 2) return response()->json(['ERROR' => ['DATA' => 'THIS PRODUCT WAS NOT SENT OR WAS ALREADY DELIVERED']], 400);

        try {
            DB::select("UPDATE VS_DELIVERY SET
                            DELIVERY_STATUS_ID = 3
                        WHERE
                            VS_ORDER_ID = {$order->ID}
                        AND
                            ID = {$delivery->ID}
                    ");

            $packages = DB::select("SELECT ID, DELIVERY_STATUS_ID FROM VS_DELIVERY WHERE VS_ORDER_ID = {$order->ID}");
            $counter = count($packages);
            $sended = 0;
            foreach ($packages as $package){
                if ($package->DELIVERY_STATUS_ID == 3) $sended++;
            }
            if($sended == $counter){
                DB::select("UPDATE VS_ORDER SET
                                DELIVERY_STATUS_ID = 3
                            WHERE
                                ID = {$order->ID}
                    ");
            }else{
                DB::select("UPDATE VS_ORDER SET
                                DELIVERY_STATUS_ID = 2
                            WHERE
                                ID = {$order->ID}
                     ");
            }

        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }

        $user = User::find($order->USER_ID);
        $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
        if (!$user){
            $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
            $user = User::find($userAccount->USER_ID);
        }

        $data = [
            'WHATSAPP' => $user->DDI . $user->PHONE,
            'NAME' => $user->NAME,
            'USER_ID' => $user->ID,
            'USER_ACCOUNT_ID' => $userAccount ? $userAccount->ID : null
        ];

        SendWhatsapp::sendMessage($data, SendWhatsapp::CONFIRM_DELIVERY);

        return (new Message())->defaultMessage(1, 200);
    }

    public function admConfirmDeliveryToClient($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_DELIVERY_ID' => 'required',
            'VS_ORDER_ID' => 'required'
        ])->validate();

        $order = $this->order->find($request->VS_ORDER_ID);
        $delivery = Delivery::find($request->VS_DELIVERY_ID);
        $adm = Adm::where('UUID', $uuid)->first();

        if(!$delivery || !$order || !$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);
        if ($delivery->DELIVERY_STATUS_ID != 2) return response()->json(['ERROR' => ['DATA' => 'THIS PRODUCT WAS NOT SENT OR WAS ALREADY DELIVERED']], 400);

        try {
            DB::select("UPDATE VS_DELIVERY SET
                            DELIVERY_STATUS_ID = 3,
                            ADM_ID = {$adm->ID},
                            DT_LAST_UPDATE_ADM = NOW()
                        WHERE
                            VS_ORDER_ID = {$order->ID}
                        AND
                            ID = {$delivery->ID}
                    ");

            $packages = DB::select("SELECT ID, DELIVERY_STATUS_ID FROM VS_DELIVERY WHERE VS_ORDER_ID = {$order->ID}");
            $counter = count($packages);
            $sended = 0;
            foreach ($packages as $package){
                if ($package->DELIVERY_STATUS_ID == 3) $sended++;
            }
            if($sended == $counter){
                DB::select("UPDATE VS_ORDER SET
                                DELIVERY_STATUS_ID = 3
                            WHERE
                                ID = {$order->ID}
                    ");
            }else{
                DB::select("UPDATE VS_ORDER SET
                                DELIVERY_STATUS_ID = 2
                            WHERE
                                ID = {$order->ID}
                     ");
            }

        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }

        $user = User::find($order->USER_ID);
        $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
        if (!$user){
            $userAccount = UserAccount::find($order->USER_ACCOUNT_ID);
            $user = User::find($userAccount->USER_ID);
        }

        $data = [
            'WHATSAPP' => $user->DDI . $user->PHONE,
            'NAME' => $user->NAME,
            'USER_ID' => $user->ID,
            'USER_ACCOUNT_ID' => $userAccount ? $userAccount->ID : null
        ];

        SendWhatsapp::sendMessage($data, SendWhatsapp::CONFIRM_DELIVERY);

        return (new Message())->defaultMessage(1, 200);
    }

    public function setRealShippingCost($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'VS_ORDER_ID' => 'required',
            'VS_DELIVERY_ID' => 'required',
            'REAL_SHIPPING_COST' => 'required'
        ])->validate();

        $order = $this->order->find($request->VS_ORDER_ID);
        $delivery = Delivery::find($request->VS_DELIVERY_ID);
        $adm = Adm::where('UUID', $uuid)->first();

        if(!$delivery || !$order || !$adm) return (new Message())->defaultMessage(17, 404);
        if((new JwtValidation())->validateByAdm($adm->ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        if ($delivery->REAL_SHIPPING_COST != '' || $delivery->REAL_SHIPPING_COST != NULL) return response()->json(['ERROR' => ['DATA' => 'REAL SHIPPING COST ALREADY REGISTERED']], 400);

        try {
            DB::select("UPDATE VS_DELIVERY SET REAL_SHIPPING_COST = '{$request->REAL_SHIPPING_COST}', ADM_ID = {$adm->ID}, DT_LAST_UPDATE_ADM = NOW() WHERE VS_ORDER_ID = {$order->ID} AND ID = {$delivery->ID}");
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT THE SUPPORT']], 400);
        }
    }
}
