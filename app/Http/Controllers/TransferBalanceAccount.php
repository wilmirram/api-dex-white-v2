<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferBalanceAccountRequest;
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

class TransferBalanceAccount extends Controller
{
    public function transferBalanceAccount(TransferBalanceAccountRequest $request)
    {

        $user = UserAccount::find($request->P_USER_ACCOUNT_ID); //BUSCA O USER ACCOUNT
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $nicknameuser = DB::select("SELECT UA.NICKNAME FROM USER_ACCOUNT UA WHERE UA.ID = {$request->P_USER_ACCOUNT_ID};");

            $result = DB::select("SELECT FN_VERIFY_FINANCIAL_PASSWORD({$request->P_USER_ACCOUNT_ID}, '{$request->P_FINANCIAL_PASSWORD}') AS fp ");

            if($result[0]->fp != 1){
                return (new Message())->defaultMessage(37, 403);
            }

            if($request->P_WITHDRAWAL_METHOD_ID == 9){

                $result = DB::select("CALL SP_TRANSFER_BALANCE_ACCOUNT({$request->P_USER_ACCOUNT_ID},
                                                                        {$request->P_WITHDRAWAL_METHOD_ID},
                                                                        {$request->P_AMOUNT},
                                                                        '{$request->P_USER_RECEIVED_BALANCE}',
                                                                        '{$request->P_FINANCIAL_PASSWORD}')");

                if($result[0]->CODE == 1){
                    return (new Message())->defaultMessage(1, 200);

                    //$preferencialWallet = (DB::select("SELECT FN_GET_WALLET_PREFERENTIAL({$request->P_USER_ACCOUNT_ID}, {$request->P_REFERENCE_ID}) as P_WALLET"))[0]->P_WALLET;
                    //$token = (new Crypto())->cryptoWithdrawalEncrypt($request->P_USER_ACCOUNT_ID, $result[0]->REGISTRATION_WITHDRAWAL_REQUEST_ID, $preferencialWallet);

                    //$mg = new MailGunFactory();

                    //$eUser = User::find($user->USER_ID);
                    //$email = explode('@', $eUser->EMAIL);

                    //$html = (new HtmlWriter($nicknameuser[0]->NICKNAME))->withDrawalRequest($token, $request->P_AMOUNT, 2, $nicknameuser[0]->NICKNAME);

                    //if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                    //    $mail = Mail::to($eUser->EMAIL)->send(new WithDrawalMail($html));
                    //    $mail = true;
                    //}else{
                    //    $mail = $mg->send($eUser->EMAIL, 'Request Transfer!', $html);
                    //}

                    //if($mail){
                    //    return (new Message())->defaultMessage(1, 200);
                    //}else{
                    //    return response()->json(['ERROR' => ['MESSAGE' => 'THERE WAS AN ERROR SENDING EMAIL']], 400);
                    //}
                } else {
                    return (new Message())->defaultMessage($result[0]->CODE, 400);
                }

            }else{

                return (new Message())->defaultMessage(13, 404);
                /*
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
                */
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }

    }
}
