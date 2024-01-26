<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithDrawlRequestRequest;
use App\Http\Requests\WithDrawlRequestConfirm;
use App\Mail\UserAccountMail;
use App\Mail\WithDrawalMail;
use App\Models\Adm;
use App\Models\Boleto;
use App\Models\TempWithdrawalTable;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserBank;
use App\Models\UserWallet;
use App\Models\WithdrawlMethod;
use App\Models\WithDrawlRequest;
use App\Utils\Crypto;
use App\Utils\HtmlWriter;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use App\Utils\SqlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Resource_;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use function GuzzleHttp\json_decode;
use PDO;

class WithDrawlRequestController extends Controller
{

    private $withdrawl;

    public function __construct(WithDrawlRequest $withdrawl)
    {
        $this->withdrawl = $withdrawl;
    }

    public function requestWithDrawal(WithDrawlRequestRequest $request)
    {
        $user = UserAccount::find($request->P_USER_ACCOUNT_ID); //BUSCA O USER ACCOUNT
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $nicknameuser = DB::select("SELECT UA.NICKNAME FROM USER_ACCOUNT UA WHERE UA.ID = {$request->P_USER_ACCOUNT_ID};");

            $result = DB::select("SELECT FN_GET_ENABLE_WITHDRAWAL_SCREEN({$request->P_USER_ACCOUNT_ID}) as enable,
                                  FN_VERIFY_FINANCIAL_PASSWORD({$request->P_USER_ACCOUNT_ID}, '{$request->P_FINANCIAL_PASSWORD}') AS fp ");

            if($result[0]->enable != 1){
                return (new Message())->defaultMessage($result[0]->enable, 400);
            }

            if($result[0]->fp != 1){
                return (new Message())->defaultMessage(37, 403);
            }

            if($request->P_WITHDRAWAL_METHOD_ID == 2){
                $result = DB::select("CALL SP_NEW_REGISTRATION_WITHDRAWAL_REQUEST({$request->P_USER_ACCOUNT_ID},
                                                                        {$request->P_WITHDRAWAL_METHOD_ID},
                                                                        {$request->P_REFERENCE_ID},
                                                                        {$request->P_AMOUNT},
                                                                       '{$request->P_FINANCIAL_PASSWORD}')");

                if($result[0]->CODE == 1){
                    $preferencialWallet = (DB::select("SELECT FN_GET_WALLET_PREFERENTIAL({$request->P_USER_ACCOUNT_ID}, {$request->P_REFERENCE_ID}) as P_WALLET"))[0]->P_WALLET;
                    $token = (new Crypto())->cryptoWithdrawalEncrypt($request->P_USER_ACCOUNT_ID, $result[0]->REGISTRATION_WITHDRAWAL_REQUEST_ID, $preferencialWallet);

                    $mg = new MailGunFactory();

                    $eUser = User::find($user->USER_ID);
                    $email = explode('@', $eUser->EMAIL);

                    $html = (new HtmlWriter($nicknameuser[0]->NICKNAME))->withDrawalRequest($token, $request->P_AMOUNT, 2, $nicknameuser[0]->NICKNAME);

                    if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                        $mail = Mail::to($eUser->EMAIL)->send(new WithDrawalMail($html));
                        $mail = true;
                    }else{
                        $mail = $mg->send($eUser->EMAIL, 'Request Transfer!', $html);
                    }

                    if($mail){
                        return (new Message())->defaultMessage(1, 200);
                    }else{
                        return response()->json(['ERROR' => ['MESSAGE' => 'THERE WAS AN ERROR SENDING EMAIL']], 400);
                    }
                }else{
                    return (new Message())->defaultMessage($result[0]->CODE, 400);
                }
            }else{
                $token = (new Crypto())->withdrawalRequestEncrypt($request->P_USER_ACCOUNT_ID, $request->P_WITHDRAWAL_METHOD_ID, $request->P_REFERENCE_ID,$request->P_AMOUNT);

                $mg = new MailGunFactory();

                $eUser = User::find($user->USER_ID);
                $email = explode('@', $eUser->EMAIL);
                //$nickname = $nicknameuser;
                //$eUser->NAME ? $eUser->NAME : $eUser->SOCIAL_REASON

                $html = (new HtmlWriter($nicknameuser[0]->NICKNAME))->withDrawalRequest($token, $request->P_AMOUNT, 1, $nicknameuser[0]->NICKNAME);

                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                    $mail = Mail::to($eUser->EMAIL)->send(new WithDrawalMail($html));
                    $mail = true;
                }else{
                    $mail = $mg->send($eUser->EMAIL, 'Solicitação de Saque!', $html);
                }

                if($mail){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'THERE WAS AN ERROR SENDING EMAIL']], 400);
                }
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function withDrawlRequest($withdrawalMethod,$token)
    {

        if($withdrawalMethod == 1){
            $token = (new Crypto())->withdrawalRequestDescrypt($token);
        }else{
            $token = (new Crypto())->cryptoWithdrawalDecrypt($token);
        }

        $user = UserAccount::find($token->uid); //BUSCA O USER ACCOUNT
        $fp = User::find($user->USER_ID);

        if($user){
            $exp = new \DateTime($token->exp->date);
            $now = new \DateTime();
            $diff = date_diff($exp, $now);
            if($diff->days >= 1){
                return redirect(env('FRONT_URL')."login#withdrawfail");
            }else{
                if($withdrawalMethod == 2){
                    $result = DB::select("CALL SP_CONFIRM_WITHDRAWAL_BY_EMAIL('{$fp->EMAIL}', {$user->ID}, {$token->wri})");
                }else{
                    $result = DB::select("CALL SP_NEW_WITHDRAWAL_REQUEST({$token->uid},
                                                                        {$token->wdm},
                                                                        {$token->ref},
                                                                        {$token->amt},
                                                                       '{$fp->FINANCIAL_PASSWORD}')");
                }

                if($result[0]->CODE == 1){
                    return redirect(env('FRONT_URL')."login#withdrawsuccess");
                }

                elseif ($result[0]->CODE == 37){
                    return redirect(env('FRONT_URL')."login#withdrawfail");
                }
                else{
                    return redirect(env('FRONT_URL')."login#withdrawfail");
                }
            }
        }else{
            return redirect(env('FRONT_URL')."login#withdrawfail");
        }
    }

    public function search($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_WITHDRAWALL('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(40, 400, null, 'SP_SEARCH_WITHDRAWALL');
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

        $withdrawal = $this->withdrawl->find($request->ID);
        if(!$withdrawal){
            return (new Message())->defaultMessage(48, 404);
        }

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){
            $json = (new MassiveJsonConverter())->generate('UPDATE', $request);
            $result = DB::select("CALL SP_UPDATE_WITHDRAWAL_REQUEST('{$json}', '{$uuid}', '', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_UPDATE_WITHDRAWAL_REQUEST');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function prepareWithdrawalSpreadReport($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'WITHDRAWAL_STATUS_ID' => 'required',
            'WITHDRAWAL_METHOD_ID' => 'required',
            'DIGITAL_PLATFORM_ID' => 'required'
        ])->validate();
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $json = (new MassiveJsonConverter())->generate('SEARCH', $request);
            $result = DB::select("CALL SP_SEARCH_PREPARE_WITHDRAWAL('{$json}', '{$uuid}', '', @P_CODE_LIST_ID);");
            $id = (DB::select("SELECT @P_CODE_LIST_ID as id"))[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_PREPARE_WITHDRAWAL');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }

    }

    public function removeWithdrawalRequests($uuid, Request $request)
    {

        Validator::make($request->all(),[
            'WITHDRAWAL_REQUEST_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $withdrawalRequests = json_decode($request->WITHDRAWAL_REQUEST_ID);

            foreach ($withdrawalRequests as $requests){
                $temp = TempWithdrawalTable::where("WITHDRAWAL_REQUEST_ID", $requests)->first();
                if($temp){
                    DB::select("DELETE FROM TABLE_TEMP_WITHDRAWAL WHERE WITHDRAWAL_REQUEST_ID = {$requests}");
                }
            }

            return (new Message())->defaultMessage(1, 200);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function spreadsheetExport(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'uuid' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $request->uuid)->first();

        if($adm){
            date_default_timezone_set ( 'America/Sao_Paulo');

            $result = DB::select("CALL SP_GET_PREPARE_WITHDRAWAL_LIST()");

            $today = date('Ymd_Hi');

            $now = date('Y-m-d');

            if($request->has('DAYS')){
                $date = date('d/m/Y', strtotime($now . " + {$request->DAYS} day"));
            }else{
                $date = date('d/m/Y', strtotime($now . " + 1 day"));
            }

            $name = str_replace(' ', '_', $request->name);

            $name = $today."_".$name;

            $dir = $_SERVER['DOCUMENT_ROOT'].'/storage/exports/';

            if (!file_exists($dir)){
                File::makeDirectory($dir);
            }

            $record = DB::select("CALL SP_RECORD_WITHDRAWAL_REQUEST_SHEET('$request->uuid', '$name')");

            if($record[0]->CODE == 1){

                $boleto = new Boleto();

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A'.'1', "CPF do funcionario");
                $sheet->setCellValue('B'.'1', 'Valor');
                $sheet->setCellValue('C'.'1', "Data de pagamento");
                $sheet->setCellValue('D'.'1', "Identificação Personalizada");

                $count = count($result);
                for ($i = 0; $i < $count; $i++){
                    $sheet->setCellValue('A'.($i+2), $result[$i]->DOCUMENT);
                    $sheet->setCellValue('B'.($i+2), $result[$i]->NET_AMOUNT);
                    $sheet->setCellValue('C'.($i+2), $date);
                    $sheet->setCellValue('D'.($i+2), $result[$i]->WITHDRAWAL_REQUEST_ID);
                }

                $writer = new Xlsx($spreadsheet);

                $writer->save('storage/exports/'.$name.'.xlsx');

                return response()->file("storage/exports/{$name}.xlsx", [ 'Content-Disposition' => "inline; filename={$name}.xlsx"]);
            }else{
                return (new Message())->defaultMessage($record[0]->CODE, 400, null, 'SP_RECORD_WITHDRAWAL_REQUEST_SHEET');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function removeAllExports($uuid)
    {
        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){
            $files = Storage::disk('public')->files('exports/');
            foreach ($files as $file){
                Storage::disk('public')->delete($file);
            }
            return response()->json(['SUCCESS' => ["ALL FILES IN THE EXPORTS FOLDER HAVE BEEN REMOVED"]], 200);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function verifySpreadSheetInFolder($filename)
    {
        $exists = Storage::disk('public')->exists("exports/{$filename}.xlsx");
        if($exists){
            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => ["THIS FILE DON'T EXIST IN THE FOLDER"]], 404);
        }

    }

    public function admWithdrawalByCrypto($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'P_WITHDRAWAL_REQUEST_ID' => 'required',
            'P_STATUS' => 'required',
            'P_CRYPTO_CURRENCY_ID' => 'required',
            'P_WALLET' => 'required',
            'P_DT_TRANSACTION' => 'required',
            'P_DESCRIPTION' => 'required'
        ])->validate();

        if(!$request->has('P_SATOSHI') || $request->P_SATOSHI == NULL || $request->P_SATOSHI == ''){
            $request['P_SATOSHI'] = 'NULL';
        }

        if(!$request->has('P_HASH_TRANSACTION_URL') || $request->P_HASH_TRANSACTION_URL == NULL || $request->P_HASH_TRANSACTION_URL == ''){
            $request['P_HASH_TRANSACTION_URL'] = 'NULL';
        }

        if(!$request->has('P_CRYPTO_QUOTE_USD') || $request->P_CRYPTO_QUOTE_USD == NULL || $request->P_CRYPTO_QUOTE_USD == ''){
            $request['P_CRYPTO_QUOTE_USD'] = 'NULL';
        }

        if(!$request->has('P_AMOUNT') || $request->P_AMOUNT == NULL || $request->P_AMOUNT == ''){
            $request['P_AMOUNT'] = 'NULL';
        }

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){
            $result = DB::select("CALL SP_CONFIRM_WITHDRAWAL_CRYPTO(
                                                                           {$request->P_WITHDRAWAL_REQUEST_ID},
                                                                           '{$request->P_STATUS}',
                                                                           {$request->P_CRYPTO_CURRENCY_ID},
                                                                           {$request->P_AMOUNT},
                                                                           {$request->P_SATOSHI},
                                                                           '{$request->P_HASH_TRANSACTION_URL}',
                                                                           {$request->P_CRYPTO_QUOTE_USD},
                                                                           '{$request->P_WALLET}',
                                                                           '{$request->P_DT_TRANSACTION}',
                                                                           '{$request->P_DESCRIPTION}',
                                                                           '{$adm->UUID}'
                                                                            )");


            //executando até aqui
            if($result[0]->CODE === 1){
                // buscar id do user
                $user2 = DB::select("SELECT WR.USER_ID,
                                            UA.NICKNAME
                                       FROM WITHDRAWAL_REQUEST WR
                                       JOIN USER_ACCOUNT UA
                                         ON UA.ID = WR.USER_ACCOUNT_ID
                                      WHERE WR.ID = $request->P_WITHDRAWAL_REQUEST_ID");
                // fim da busca

                // codigo para enviar e-mail
                //$user = UserAccount::find($request->P_USER_ACCOUNT_ID); //BUSCA O USER ACCOUNT
                $eUser = User::find($user2[0]->USER_ID);
                $nome = 'ASSOCIADO WHITECLUB';
                $mg = new MailGunFactory();

                $html = (new HtmlWriter($nome))->withDrawalconcluido($request->P_SATOSHI, $request->P_DT_TRANSACTION, $request->P_HASH_TRANSACTION_URL, $user2[0]->NICKNAME);

                $mail = $mg->send($eUser->EMAIL, 'Retirada Realizada com Sucesso', $html);

                if($mail){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'THERE WAS AN ERROR SENDING EMAIL']], 400);
                }

                // fim do codigo

            }else{
                return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_CONFIRM_WITHDRAWAL_CRYPTO');
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function callbackSpreadSheet(Request $request)
    {
        //token: E2FE9EB29A47F4FCF5B37D3F9D8D872EF43372FEBE2CD2
        if(!$request->hasHeader('token') ||
            strlen($request->header('token')) != 46 ||
            $request->header('token') != env("CALLBACK_TOKEN")){
            return response()->json(['ERROR' => ["MESSAGE" => "ACCESS DENIED"]], 403);
        }

        Validator::make($request->all(), [
            "documentNumber" => 'required',
            "transactionDate" => 'required',
            "amount" => 'required',
            "status" => 'required',
            "externalId" => 'required'
        ])->validate();

        $query = "CALL SP_CONFIRM_WITHDRAWAL(
            {$request->externalId},
             1,
              '{$request->documentNumber}',
               '{$request->transactionDate}',
                '{$request->amount}',
                 '{$request->status}',
                  '{$request->transaction_id}',
                   '{$request->description}',
                    '77de68daecd823babbb58edb1c8e14d7106e83bb')";

        $result = SqlHelper::run($query);

        /*
         * $result = DB::select("CALL SP_CONFIRM_WITHDRAWAL(
            {$request->externalId},
             1,
              '{$request->documentNumber}',
               '{$request->transactionDate}',
                '{$request->amount}',
                 '{$request->status}',
                  '{$request->transaction_id}',
                   '{$request->description}',
                    '77de68daecd823babbb58edb1c8e14d7106e83bb')");
        */

        return (new Message())->defaultMessage(1, 200);
    }
}
