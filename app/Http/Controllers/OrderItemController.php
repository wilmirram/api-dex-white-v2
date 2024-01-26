<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderItemRequest;
use App\Mail\TermosDeUsoMail;
use App\Models\Adm;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SendWhatsapp;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\VS\Order;
use App\ProdutoFoto;
use App\Utils\FileHandler;
use App\Utils\HtmlWriter;
use App\Utils\Invoice;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Aws\DataExchange\DataExchangeClient;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class OrderItemController extends Controller
{
    private $ordemItem;

    public function __construct(OrderItem $ordemItem)
    {
        $this->ordemItem = $ordemItem;
    }
/*
    public function generateInvoice($invoiceID, $send)
    {
        $invoice = Invoice::generateOne($invoiceID, $send ? true : false, $send ? true : false);
        if (!$invoice) return response()->json('invoice not found', 404);
        return $invoice['pdf']->stream('invoice.pdf');
    }
*/
    public function index()
    {
        $data = $this->ordemItem->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->ordemItem->find($id);
        if(!$data){
            return (new Message())->defaultMessage(17, 404);
        }else{
            return response()->json($data);
        }
    }

    public function store(OrderItemRequest $request)
    {
        $user = UserAccount::find($request->P_USER_ACCOUNT_ID);
        if($user){
            
            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $product = Product::find($request->P_PRODUCT_ID);
            if($product){
                $result = DB::select("CALL SP_NEW_ORDER_ITEM('{$request->P_USER_ACCOUNT_ID}', '{$request->P_PRODUCT_ID}')");
                
                if($result[0]->CODE == 1){
                    $nickname = $user->NICKNAME;
                    $testename = $user->NAME;
                    $user = User::find($user->USER_ID);
                    $html = (new HtmlWriter($user->NAME))->termosDeUso($testename, $nickname);
                    $mg = new MailGunFactory();
                    $email = explode('@', $user->EMAIL);
                    if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                        $mail = Mail::to($user->EMAIL)->send(new TermosDeUsoMail($html));
                        $mail = true;
                    }else{
                        $mail = $mg->send($user->EMAIL, 'TERMOS E CONDIÇÕES DE USO', $html, ['filePath' => 'storage/termos/termoDeCompra.pdf', 'filename' => 'termoDeCompra.pdf']);
                    }
                    $lastOrder = DB::select("SELECT MAX(ID) AS ORDER_ITEM_ID
                                               FROM ORDER_ITEM
                                              WHERE USER_ACCOUNT_ID = {$request->P_USER_ACCOUNT_ID}");

                    DB::select("UPDATE ORDER_ITEM
                                   SET TERMS_OF_USE_SUBMITTED = 1,
                                       DT_TERMS_OF_USE_SUBMITTED = NOW()
                                 WHERE ID = {$lastOrder[0]->ORDER_ITEM_ID}");
                    return (new Message())->defaultMessage(1, 200);
                }elseif ($result[0]->CODE == 12){
                    return (new Message())->defaultMessage(12, 400);
                }else{
                    return (new Message())->defaultMessage(20, 400);
                }
            }else{
                return (new Message())->defaultMessage(17, 404);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function update($id, Request $request)
    {
        $ordem = $this->ordemItem->find($id);
        if(!$ordem){
            return (new Message())->defaultMessage(17, 404);
        }

        foreach ($request->all() as $key => $value) {
            DB::select("UPDATE ORDER_ITEM SET {$key} = '{$value}' WHERE id = {$id}");
        }

        return (new Message())->defaultMessage(22, 203);
    }

    public function delOrderItemOpen(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_ORDER_ITEM_ID' => 'required'
        ])->validate();

        $user = UserAccount::find($request->P_USER_ACCOUNT_ID);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $order = $this->ordemItem->find($request->P_ORDER_ITEM_ID);
            if($order){
                $result = DB::select("CALL SP_DEL_ORDER_ITEM_OPEN('{$request->P_USER_ACCOUNT_ID}', '{$request->P_ORDER_ITEM_ID}')");
                if($result[0]->CODE == 1){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'INVALID ORDER ITEM']], 400);
                }
            }else{
                return (new Message())->defaultMessage(14, 404);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function addPaymentVoucher($id, Request $request)
    {
        Validator::make($request->all(), [
            'PAYMENT_VOUCHER' => 'required',
        ])->validate();

            $order = $this->ordemItem->find($id);
            if($order){
                if($order->STATUS_ORDER_ID != 1){
                    return response()->json(['ERROR' => ['MESSAGE' => 'YOU CAN NOT CHANGE THE DATA OF THIS ORDER']], 400);
                }
                if($order->PAYMENT_VOUCHER != NULL){
                    return response()->json(['ERROR' => ['MESSAGE' => 'THIS ORDER JUST HAVE A VOUCHER']], 400);
                }

                $base = (explode(';', $request->PAYMENT_VOUCHER))[0];
                $type = (explode('/', $base))[1];

                if($type != 'png' && $type != 'jpeg' && $type != 'jpg' && $type != 'pdf'){
                    return response()->json(['ERROR' => ['MESSAGE' => 'INVALID VOUCHER FORMAT TYPE']], 400);
                }

                $file = (new FileHandler())->writeFile($request->PAYMENT_VOUCHER, 'voucher', $order->ID);

                if($request->has('PAYMENT_METHOD_ID') && $request->PAYMENT_METHOD_ID != null){
                    DB::select("UPDATE ORDER_ITEM SET PAYMENT_VOUCHER = '{$file}', DT_PAYMENT_VOUCHER = NOW(), PAYMENT_METHOD_ID = {$request->PAYMENT_METHOD_ID} WHERE id = {$id}");
                }else{
                    DB::select("UPDATE ORDER_ITEM SET PAYMENT_VOUCHER = '{$file}', DT_PAYMENT_VOUCHER = NOW() WHERE id = {$id}");
                }
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
    }

    public function cryptoPayment($id, Request $request)
    {
        Validator::make($request->all(), [
            'PAYMENT_VOUCHER' => 'required',
            'PAYMENT_METHOD_ID' => 'required',
            'CRYPTO_CURRENCY_ID' => 'required',
            'CRYPTO_QUOTE_BRL' => 'required',
            'CRYPTO_AMOUNT' => 'required',
            'CRYPT_HASH' => 'required'
        ])->validate();

        $order = $this->ordemItem->find($id);
        if($order){
            if($order->STATUS_ORDER_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'YOU CAN NOT CHANGE THE DATA OF THIS ORDER']], 400);
            }
            if($order->PAYMENT_VOUCHER != NULL){
                return response()->json(['ERROR' => ['MESSAGE' => 'THIS ORDER JUST HAVE A VOUCHER']], 400);
            }

            $fee = ((($order->NET_PRICE * 5) / 100) * 3);
            $brlTotal = ($order->NET_PRICE * 5) + $fee;
            $amount = $brlTotal * $request->CRYPTO_QUOTE_BRL;

            if ($amount != $request->CRYPTO_AMOUNT) return response()->json(['ERROR' => ['MESSAGE' => 'INVALID AMOUNT']], 400);

            //$quotation = Http::get("https://blockchain.info/tobtc?currency=BRL&value=1"); //VERIFICA A COTAÇÃO DO BTC
            //if ((double) $quotation->body() < $request->CRYPTO_QUOTE_BRL) return response()->json(['ERROR' => ['MESSAGE' => 'ACTUAL QUOTATION IS INFERIOR THAN THE SENT QUOTATION']], 400);

            /**
            $variation = ($request->CRYPTO_QUOTE_BRL * 100 / $quotation) - 100;
            */

            $base = (explode(';', $request->PAYMENT_VOUCHER))[0];
            $type = (explode('/', $base))[1];

            if($type != 'png' && $type != 'jpeg' && $type != 'jpg' && $type != 'pdf'){
                return response()->json(['ERROR' => ['MESSAGE' => 'INVALID VOUCHER FORMAT TYPE']], 400);
            }
            $file = (new FileHandler())->writeFile($request->PAYMENT_VOUCHER, 'voucher', $order->ID);

            try {
                DB::select("
                            UPDATE ORDER_ITEM SET
                                PAYMENT_VOUCHER = '{$file}',
                                DT_PAYMENT_VOUCHER = NOW(),
                                PAYMENT_METHOD_ID = {$request->PAYMENT_METHOD_ID},
                                CRYPTO_CURRENCY_ID = {$request->CRYPTO_CURRENCY_ID},
                                CRYPTO_ADDRESS = 'bc1qhxfsvanedahz2lfdgd7n4x6jklduw2uwpftf6p',
                                CRYPTO_QUOTE_BRL = '{$request->CRYPTO_QUOTE_BRL}',
                                CRYPTO_AMOUNT = '{$request->CRYPTO_AMOUNT}',
                                CRYPTO_QR_CODE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJQAAACUCAYAAAB1PADUAAAAAklEQVR4AewaftIAAATCSURBVO3BQQ4jRxIEwfAC//9l3znmqYBGJymNNszwj1QtOaladFK16KRq0UnVopOqRSdVi06qFp1ULTqpWnRSteikatFJ1aKTqkUnVYtOqhZ98hKQX1IzAblRMwGZ1GwCcqPmCSC/pOaNk6pFJ1WLTqoWfbJMzSYgN2omIDdqJiCTmieATGpugNyouVGzCcimk6pFJ1WLTqoWffJlQJ5Q801AJjUTkEnNBOQGyI2aTUCeUPNNJ1WLTqoWnVQt+uQvB2RSMwGZ1ExAJjUTkE1AbtT8zU6qFp1ULTqpWvTJfwyQSc0E5AbIjZoJyKTmCSD/JSdVi06qFp1ULfrky9T8kpoJyKTmBsgNkEnNBGRSMwGZ1Lyh5t/kpGrRSdWik6pFnywD8jcBMqmZgExqJiCTmgnIpGYCMqm5AfJvdlK16KRq0UnVok9eUvM3U/OGmgnIpOZGzY2av8lJ1aKTqkUnVYvwj7wAZFLzBJBJzQTkCTVvAHlCzRtAvknNDZBJzRsnVYtOqhadVC3CP/ICkBs1E5BJzRtAJjU3QCY1/2ZAfknNGydVi06qFp1ULfrkJTUTkBs1E5BvAjKpuQHyTWomIJOaSc0bQCY1E5BNJ1WLTqoWnVQt+uTHgExqJiCTmhsgTwCZ1NyomYBMaiYg3wTkCTW/dFK16KRq0UnVok/+YUAmNROQGzU3am6AfJOaJ4BMam7U3ACZ1ExqNp1ULTqpWnRSteiTLwNyo+ZGzQTkBsikZgLyBJBJzQRkUnMDZFLzBpAbNROQGzVvnFQtOqladFK16JMvU3MDZFIzAZnUvKFmAjKpuQFyA+RGzQ2QGzUTkEnNBOSXTqoWnVQtOqla9MkyNTdAJjU3aiYgvwTkDTUTkBs1E5AbNROQSc0NkE0nVYtOqhadVC365CUgT6iZgDyh5gbIDZBJzQTkRs0E5A01N2omIE8A+aWTqkUnVYtOqhZ98mNAnlDzBpAbIJOaGyA3aiYgk5oJyC+p+aaTqkUnVYtOqhbhH/kiIN+kZhOQb1JzA+QJNTdAJjUTkEnNGydVi06qFp1ULfrky9RMQCY1E5BJzQTkCSBvqLkBcqNmAjKpuVFzA+RGzS+dVC06qVp0UrXok5eA3Kh5A8ik5gbIjZobIDdAJjUTkCeA3AB5Qs0EZFIzqdl0UrXopGrRSdUi/CN/MSCTmieAPKFmAjKpuQFyo+YJIJvUvHFSteikatFJ1aJPXgLyS2pugExqnlBzA+QJIG8AmdTcqLkB8k0nVYtOqhadVC36ZJmaTUBu1ExAboA8AeQJIJOaCcgTap4AMqm5UbPppGrRSdWik6pFn3wZkCfUPAHkCTU3QJ5QMwF5A8gmIJOabzqpWnRSteikatEnfzk1E5AbIE+omYBMQCY1E5AbNROQSc0NkDeATGreOKladFK16KRq0Sf/Z9RMQCY1E5AbNTdqvknNBGRSMwH5ppOqRSdVi06qFn3yZWr+SUDeUDMBuQHyS0D+TU6qFp1ULTqpWvTJMiC/BGRSMwF5AsiNmifU3AB5Q80E5Ak1m06qFp1ULTqpWoR/pGrJSdWik6pFJ1WLTqoWnVQtOqladFK16KRq0UnVopOqRSdVi06qFp1ULTqpWnRSteh/fKEOc/vKPnQAAAAASUVORK5CYII=',
                                CRYPTO_HASH = '{$request->CRYPT_HASH}'
                            WHERE ID = {$id}");
            }catch (\Exception $exception){
                return response()->json(['ERROR' => ['MESSAGE' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);
            }
            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function removeVoucher($id)
    {
        $order = $this->ordemItem->find($id);
        if($order){

            if($order->STATUS_ORDER_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'YOU CAN NOT CHANGE THE DATA OF THIS ORDER']], 400);
            }

            if($order->PAYMENT_VOUCHER == NULL || $order->PAYMENT_VOUCHER == ''){
                return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER DON'T HAVE A VOUCHER"]], 400);
            }else{
                $voucher = $order->PAYMENT_VOUCHER;

                if((new FileHandler())->removeFile($voucher) == true){
                    DB::select("UPDATE ORDER_ITEM SET PAYMENT_VOUCHER = NULL, DT_PAYMENT_VOUCHER = NULL WHERE ID = {$id}");
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "ERROR OCCURRED WHEN REMOVING THE IMAGE"]], 400);
                }
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }

    }

    public function getVoucher($id)
    {
        $order = $this->ordemItem->find($id);
        if($order){
            if($order->PAYMENT_VOUCHER === null || $order->PAYMENT_VOUCHER === ''){
                return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER DON'T HAVE A VOUCHER"]], 400);
            }else{
                try {
                    $file = (new FileHandler())->getFile($order->PAYMENT_VOUCHER);
                    $voucher = explode('.', $order->PAYMENT_VOUCHER);
                    $voucherLink = ['Name' => $voucher[0],
                        'Ext' => $voucher[1],
                        'Data' => $file];
                    return (new Message())->defaultMessage(1, 200, $voucherLink);
                }catch (\Exception $e){
                    return response()->json(['ERROR' => ["MESSAGE" => "VOUCHER DOESN'T EXISTS"]], 400);
                }
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function clearPaymentMethod($id, Request $request)
    {
        $order = $this->ordemItem->find($id);
        if($order){

            if ((new JwtValidation())->validateByUserAccount($order->USER_ACCOUNT_ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            if($order->STATUS_ORDER_ID != 1){
                return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER CAN'T BE CHANGED"]], 400);
            }

            DB::select("UPDATE ORDER_ITEM SET PAYMENT_METHOD_ID = NULL WHERE ID = {$id}");

            return (new Message())->defaultMessage(1, 200);

        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function search($uuid, Request $request)
    {
        if($request->has('ORDER_ITEM_ID') || $request->has('PRODUCT_NAME') || $request->has('USER_NAME')){
            return response()->json(['ERROR' => ["MESSAGE" => "ORDER_ITEM_ID, USER_NAME AND PRODUCT_NAME ISN'T A VALID FIELD"]], 400);
        }
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_ORDER_ITEM('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(40, 400, null, 'SP_SEARCH_ORDER_ITEM');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function exportsSearch($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_ORDER_ITEM('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");

            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                date_default_timezone_set ( 'America/Sao_Paulo');
                $today = date('Ymd_Hi');
                $name = "ORDER_REPORT_{$today}";

                $dir = $_SERVER['DOCUMENT_ROOT'].'/storage/exports/';

                if (!file_exists($dir)){
                    File::makeDirectory($dir);
                }

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A'.'1', "ORDER_ITEM_ID");
                $sheet->setCellValue('B'.'1', "USER_ACCOUNT_ID");
                $sheet->setCellValue('C'.'1', "NICKNAME");
                $sheet->setCellValue('D'.'1', "NAME");
                $sheet->setCellValue('E'.'1', "TYPE_DOCUMENT");
                $sheet->setCellValue('F'.'1', "DOCUMENT");
                $sheet->setCellValue('G'.'1', "PRODUCT_NAME");
                $sheet->setCellValue('H'.'1', "GLOSS_PRICE");
                $sheet->setCellValue('I'.'1', "NET_PRICE");
                $sheet->setCellValue('J'.'1', "DISCOUNT");
                $sheet->setCellValue('K'.'1', "UPGRADE");
                $sheet->setCellValue('L'.'1', "AMOUNT_RECEIVED_BRL");
                $sheet->setCellValue('M'.'1', "PAYMENT_METHOD");
                $sheet->setCellValue('N'.'1', "HASH");
                $sheet->setCellValue('O'.'1', "BILLET_DIGITABLE_LINE");
                $sheet->setCellValue('P'.'1', "BILLET_NET_PRICE");
                $sheet->setCellValue('Q'.'1', "PRODUCT_SCORE");
                $sheet->setCellValue('R'.'1', "LAUNCHED_SCORE");
                $sheet->setCellValue('S'.'1', "TOTAL_LAUNCHED_SCORE");
                $sheet->setCellValue('T'.'1', "STATUS_ORDER");
                $sheet->setCellValue('U'.'1', "DT_REGISTER");
                $sheet->setCellValue('V'.'1', "DT_PAYMENT");
                $sheet->setCellValue('W'.'1', "ADM_OPERATOR");

                $count = count($result);
                for ($i = 0; $i < $count; $i++){
                    $BRL = $result[$i]->PRICE * 5;
                    $sheet->setCellValue('A'.($i+2), $result[$i]->ORDER_ITEM_ID);
                    $sheet->setCellValue('B'.($i+2), $result[$i]->USER_ACCOUNT_ID);
                    $sheet->setCellValue('C'.($i+2), $result[$i]->NICKNAME);
                    $sheet->setCellValue('D'.($i+2), $result[$i]->NAME);
                    $sheet->setCellValue('E'.($i+2), $result[$i]->TYPE_DOCUMENT);
                    $sheet->setCellValue('F'.($i+2), $result[$i]->DOCUMENT);
                    $sheet->setCellValue('G'.($i+2), $result[$i]->PRODUCT_NAME);
                    $sheet->setCellValue('H'.($i+2), $result[$i]->GLOSS_PRICE);
                    $sheet->setCellValue('I'.($i+2), $result[$i]->NET_PRICE);
                    $sheet->setCellValue('J'.($i+2), $result[$i]->DISCOUNT);
                    $sheet->setCellValue('K'.($i+2), $result[$i]->UPGRADE);
                    $sheet->setCellValue('L'.($i+2), $BRL);
                    $sheet->setCellValue('M'.($i+2), $result[$i]->PAYMENT_METHOD);
                    $sheet->setCellValue('N'.($i+2), $result[$i]->HASH);
                    $sheet->setCellValue('O'.($i+2), $result[$i]->BILLET_DIGITABLE_LINE);
                    $sheet->setCellValue('P'.($i+2), $result[$i]->BILLET_NET_PRICE);
                    $sheet->setCellValue('Q'.($i+2), $result[$i]->PRODUCT_SCORE);
                    $sheet->setCellValue('R'.($i+2), $result[$i]->LAUNCHED_SCORE);
                    $sheet->setCellValue('S'.($i+2), $result[$i]->TOTAL_LAUNCHED_SCORE);
                    $sheet->setCellValue('T'.($i+2), $result[$i]->STATUS_ORDER);
                    $sheet->setCellValue('U'.($i+2), $result[$i]->DT_REGISTER);
                    $sheet->setCellValue('V'.($i+2), $result[$i]->DT_PAYMENT);
                    $sheet->setCellValue('W'.($i+2), $result[$i]->ADM_NAME);
                }

                $writer = new Xlsx($spreadsheet);

                $writer->save('storage/exports/'.$name.'.xlsx');

                return response()->file("storage/exports/{$name}.xlsx", [ 'Content-Disposition' => "inline; filename={$name}.xlsx"]);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_ORDER_ITEM');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function admUpdate($uuid, Request $request)
    {
        Validator::make($request->all(), [
           'ID' => 'required'
        ])->validate();

        $order = $this->ordemItem->find($request->ID);
        if(!$order){
            return (new Message())->defaultMessage(13, 404);
        }

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){
            $json = (new MassiveJsonConverter())->generate('UPDATE', $request);
            $result = DB::select("CALL SP_UPDATE_ORDER_ITEM('{$json}', '{$uuid}', '', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_UPDATE_ORDER_ITEM');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getTransfer(Request $request)
    {
        Validator::make($request->all(),[
            'hash' => 'required',
            'order_item_id' => 'required',
            'user_account_id' => 'required',
            'digital_platform_id' => 'required'
        ])->validate();

        $order = $this->ordemItem->find($request->order_item_id);
        if($order){

            $account = 'NULL';
            if ($request->user_account_id != 0){
                $userAccount = UserAccount::find($request->user_account_id);
                if(!$userAccount) return (new Message())->defaultMessage(13, 404);
                $account = $userAccount->ID;
            }

                if($order->STATUS_ORDER_ID == 1){
                    $code=20;
                    $existing_hash = (DB::select("SELECT FN_EXISTING_HASH(3, '{$request->hash}') as hash"))[0]->hash;
                    if($existing_hash != 0){
                        DB::select("CALL SP_RECORD_TRANSFER_PAYMENT_LOG(
                                3,
                                '{$request->hash}',
                                {$account},
                                {$request->order_item_id},
                                56,
                                NULL,
                                NULL
                             )");
                        return (new Message())->defaultMessage(56, 400);
                    }else{
                        DB::select("CALL SP_RECORD_TRANSFER_PAYMENT_LOG(
                            3,
                            '{$request->hash}',
                            {$account},
                            {$request->order_item_id},
                            {$code},
                            NULL,
                            NULL
                            )");
                    }
            
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "THIS ORDER CAN'T BE CHANGED"]], 400);
                }
            
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

            $result = DB::select("CALL SP_REPORT_PAYMENT_ORDER('{$request->P_DT_PAYMENT_START}', '{$request->P_DT_PAYMENT_END}')");

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

            $result = DB::select("CALL SP_REPORT_PAYMENT_ORDER('{$request->P_DT_PAYMENT_START}', '{$request->P_DT_PAYMENT_END}')");

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A'.'1', "ORDER_ITEM_ID");
            $sheet->setCellValue('B'.'1', 'NICKNAME');
            $sheet->setCellValue('C'.'1', "NAME");
            $sheet->setCellValue('D'.'1', "TYPE_DOCUMENT");
            $sheet->setCellValue('E'.'1', "DOCUMENT");
            $sheet->setCellValue('F'.'1', "PRODUCT");
            $sheet->setCellValue('G'.'1', "DT_PAYMENT");
            $sheet->setCellValue('H'.'1', "PRODUCT_PRICE");
            $sheet->setCellValue('I'.'1', "GLOSS_AMOUNT_RECEIVED");
            $sheet->setCellValue('J'.'1', "BILLET_FEE");
            $sheet->setCellValue('K'.'1', "AMOUNT_RECEIVED_BRL");
            $sheet->setCellValue('L'.'1', "PAYMENT_METHOD");
            $sheet->setCellValue('M'.'1', "ADM_OPERATOR");

            for ($i = 0; $i < count($result); $i++){
                $sheet->setCellValue('A'.($i+2), $result[$i]->ORDER_ITEM_ID);
                $sheet->setCellValue('B'.($i+2), $result[$i]->NICKNAME);
                $sheet->setCellValue('C'.($i+2), $result[$i]->NAME);
                $sheet->setCellValue('D'.($i+2), $result[$i]->TYPE_DOCUMENT);
                $sheet->setCellValue('E'.($i+2), $result[$i]->DOCUMENT);
                $sheet->setCellValue('F'.($i+2), $result[$i]->PRODUCT);
                $sheet->setCellValue('G'.($i+2), $result[$i]->DT_PAYMENT);
                $sheet->setCellValue('H'.($i+2), $result[$i]->PRODUCT_PRICE);
                $sheet->setCellValue('I'.($i+2), $result[$i]->GLOSS_AMOUNT_RECEIVED);
                $sheet->setCellValue('J'.($i+2), $result[$i]->BILLET_FEE);
                $sheet->setCellValue('K'.($i+2), $result[$i]->AMOUNT_RECEIVED_BRL);
                $sheet->setCellValue('L'.($i+2), $result[$i]->PAYMENT_METHOD);
                $sheet->setCellValue('M'.($i+2), $result[$i]->ADM_OPERATOR);
            }

            $name = date('Y-m-d')."_PAYMENT-REPORT";

            $writer = new Xlsx($spreadsheet);

            $writer->save('storage/exports/'.$name.'.xlsx');

            return response()->file("storage/exports/{$name}.xlsx", [ 'Content-Disposition' => "inline; filename={$name}.xlsx"]);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
