<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationRequestMail;
use App\Models\Adm;
use App\Models\UserAccount;
use App\Utils\Crypto;
use App\Utils\HtmlWriter;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use App\Utils\SqlHelper;
use App\Utils\StoredProcedures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrationRequest;
use App\Http\Requests\RegistrationRequestRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegistrationRequestController extends Controller
{

    private $rr;

    public function __construct(RegistrationRequest $request)
    {
        $this->rr = $request;
        date_default_timezone_set ( 'America/Sao_Paulo');
    }

    public function index()
    {
        $data = $this->rr->all();
        return response()->json($data);
    }

    public function store(RegistrationRequestRequest $request)
    {
        $query = "SELECT FN_EXISTING_EMAIL('{$request->P_EMAIL}') as email,
                  FN_REGISTERED_USER('{$request->P_EMAIL}') as user,
                  FN_EXISTING_DOCUMENT('{$request->P_DOCUMENT}') as document,
                  FN_EXISTING_NICKNAME('{$request->P_NICKNAME}', NULL) as nickname";
        $verify = SqlHelper::exec($query);

        if($verify['email'] == 1 && $verify['user'] == 1){
            return (new Message())->defaultMessage(3, 400);
        }
        elseif($verify['email'] == 1){
            return (new Message())->defaultMessage(24, 400);
        }

        if($verify['document'] == 1) {
            return (new Message())->defaultMessage(6, 400);
        }

        $sponsor = UserAccount::where('NICKNAME', $request->P_SPONSOR_NICKNAME)->first();
        if(!$sponsor){
            return (new Message())->defaultMessage(2, 400);
        }

        if($verify['nickname'] == 1){
            return (new Message())->defaultMessage(8, 400);
        }

        $flag = 0;

        $side = "NULL";

        if(!$request->has('P_PASSWORD') || $request->P_PASSWORD == null || $request->P_PASSWORD == ''){
            $side = $request->P_SIDE;
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 13; $i++) {
                $randomString .= $characters[Rand(0, $charactersLength - 1)];
            }

            $request->P_PASSWORD = $randomString;
            $flag = 1;
        }

        $query = "CALL SP_NEW_REGISTRATION_REQUEST(
                                                    1,
                                                    '{$request->P_NAME}',
                                                    '{$request->P_EMAIL}',
                                                    '{$sponsor->UUID}',
                                                    '{$request->P_NICKNAME}',
                                                    '{$request->P_PASSWORD}',
                                                    {$request->P_TYPE_DOCUMENT_ID},
                                                    '{$request->P_DOCUMENT}',
                                                    {$side},
                                                    @ID)";

        $result = SqlHelper::execParamQuery($query, "SELECT @ID as ID");
        
        if($result['result']['CODE'] == 1){
            $requestUser = $this->rr->find($result['param']['ID']);
            $token = (new Crypto())->encrypt($requestUser->ID, $requestUser->EMAIL);
            if($flag == 1){
                $html = (new HtmlWriter($requestUser->NICKNAME))->validateDataEmail($token, $request->P_PASSWORD);
            }else{
                $html = (new HtmlWriter($requestUser->NICKNAME))->validateDataEmail($token);
            }
            $mg = new MailGunFactory();
            //return response()->json($mg = new MailGunFactory());
            
            $sql = "UPDATE REGISTRATION_REQUEST
                            SET
                            DT_SEND = IF (SEND = 1, NOW() , NULL),
                            SEND = 0,
                            DT_RESEND  =  IF (RESEND = 1, NOW() , NULL),
                            RESEND = 0
                        WHERE ID = {$requestUser->ID} and EMAIL = '{$requestUser->EMAIL}'";
            SqlHelper::run($sql);

            $email = explode('@', $request->P_EMAIL);
            if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                $mail = Mail::to($request->P_EMAIL)->send(new RegistrationRequestMail($html));
                $mail = true;
            }else{
                $mail = $mg->send($requestUser->EMAIL, 'Confirme seus dados', $html);
            }

            if($mail){
                $date = date('Y-m-d H:i:s');
                $sql = "UPDATE REGISTRATION_REQUEST SET SEND = '0', DT_SEND = '{$date}' WHERE id = {$requestUser->ID}";
                SqlHelper::run($sql);
                return response()->json(['SUCCESS' => ['MESSAGE' => 'INSTRUCTIONS TO COMPLETE YOUR REGISTRATION WERE SENT TO YOUR EMAIL', 'NAME' => $request->P_NAME]]);
            }else{
                return (new Message())->defaultMessage(20, 500);
            }
        }else{
            return (new Message())->defaultMessage(20, 400);
            //return response()->json('resultado diferente de 1');
        }
        
    }

    public function removeRegistrationRequest(Request $request)
    {
        Validator::make($request->all(), [
            'P_REGISTRATION_REQUEST_ID' => 'required'
        ])->validate();

        $result = DB::select("CALL SP_DELETE_REGISTRATION_REQUEST({$request->P_REGISTRATION_REQUEST_ID})");

        if ($result[0]->CODE == 1) {
            return (new Message())->defaultMessage(1, 200);
        }

        return (new Message())->defaultMessage($result[0]->CODE, 400);
    }

    public function admRegistrationRequest(Request $request)
    {
        Validator::make($request->all(), [
            'P_EMAIL' => 'required|email',
            'P_NAME' => 'required',
            'P_ADM_UUID' => 'required',
            'P_TYPE_DOCUMENT_ID' => 'required',
            'P_DOCUMENT' => 'required',
            'P_TRANSFER_USER_ACCOUNT_ID' => 'required',
        ])->validate();

        $adm = Adm::where('UUID', $request->P_ADM_UUID)->first();

        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 13; $i++) {
                $randomString .= $characters[Rand(0, $charactersLength - 1)];
            }

            $result = DB::select("CALL SP_INSERT_REGISTRATION_REQUEST (?,?,?,?,?,?,?,@P_REGISTRATION_REQUEST_ID)",
                array(
                    $request->P_ADM_UUID,
                    $request->P_NAME,
                    $request->P_EMAIL,
                    $randomString,
                    $request->P_TYPE_DOCUMENT_ID,
                    $request->P_DOCUMENT,
                    $request->P_TRANSFER_USER_ACCOUNT_ID,
                    "@P_REGISTRATION_REQUEST_ID"
                ));

            if($result[0]->CODE == 1){
                $id = DB::select("SELECT @P_REGISTRATION_REQUEST_ID as id")[0]->id;
                $requestUser = $this->rr->find($id);
                $token = (new Crypto())->encrypt($requestUser->ID, $requestUser->EMAIL);
                $html = (new HtmlWriter($requestUser->NAME ? $requestUser->NAME : $requestUser->SOCIAL_REASON))->validateDataEmail($token, $randomString);
                $mg = new MailGunFactory();
                DB::select("UPDATE REGISTRATION_REQUEST
                            SET
                            DT_SEND = IF (SEND = 1, NOW() , NULL),
                            SEND = 0,
                            DT_RESEND  =  IF (RESEND = 1, NOW() , NULL),
                            RESEND = 0
                        WHERE ID = {$requestUser->ID} and EMAIL = '{$requestUser->EMAIL}'");

                $email = explode('@', $request->P_EMAIL);
                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                    $mail = Mail::to($request->P_EMAIL)->send(new RegistrationRequestMail($html));
                    $mail = true;
                }else{
                    $mail = $mg->send($requestUser->EMAIL, 'Confirme seus dados', $html);
                }

                if($mail){
                    $date = date('Y-m-d H:i:s');
                    DB::select("UPDATE REGISTRATION_REQUEST SET SEND = '0', DT_SEND = '{$date}' WHERE id = {$requestUser->ID}");
                    return response()->json(['SUCCESS' => ['MESSAGE' => 'INSTRUCTIONS TO COMPLETE YOUR REGISTRATION WERE SENT TO YOUR EMAIL']]);
                }else{
                    return (new Message())->defaultMessage(20, 500);
                    //return response()->json(["ERROR" => "INVALID OLD PASSWORD"], 400);
                }
            }else{
                return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_INSERT_REGISTRATION_REQUEST');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function show($id)
    {
        $data = $this->rr->find($id);
        if(!$data){
            return (new Message())->defaultMessage(7, 404);
        }else{
            return response()->json($data);
        }
    }

    
    public function validateAccount($token)
    {
        $data = (new Crypto())->decrypt($token);
        if(is_null($data)){
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        }
        if(!property_exists($data, 'id') || !property_exists($data, 'email') || !property_exists($data, 'exp')){
            return response()->json(['ERROR' => ['MESSAGE' => 'INCOMPLETE TOKEN']], 403);
        }
        $request = $this->rr->find($data->id);
        if($request){
            $token = new \DateTime($data->exp->date);
            $now = new \DateTime();
            $diff = date_diff($token, $now);
            if($diff->days >= 1){
                if($request->EXTERNAL_CLIENT === 1){
                    return redirect(env('EXTERNAL_STORE_URL')."confirm-account/fail");
                }else{
                    return redirect(env('FRONT_URL')."confirm-account/fail");
                }
            }else{
                if($request->EMAIL === $data->email) {
                    if($request->VERIFIED_EMAIL == 0){
                        $date = date('Y-m-d H:i:s');
                        DB::select("UPDATE REGISTRATION_REQUEST SET VERIFIED_EMAIL = '1', DT_VERIFIED_EMAIL = '{$date}' WHERE id = {$request->ID} and EMAIL = '{$request->EMAIL}'");
                        $query = "CALL SP_NEW_USER(1,{$request->ID})";
                        $result = SqlHelper::exec($query);
                        if($result['CODE'] == 1){
                            if($request->EXTERNAL_CLIENT === 1){
                                return redirect(env('EXTERNAL_STORE_URL')."confirm-account/success");
                            }else{
                                return redirect(env('FRONT_URL')."confirm-account/success");
                            }
                        }else{
                            if($request->EXTERNAL_CLIENT === 1){
                                return redirect(env('EXTERNAL_STORE_URL')."confirm-account/fail");
                            }else{
                                return redirect(env('FRONT_URL')."confirm-account/fail");
                            }
                        }
                    }else{
                        if($request->EXTERNAL_CLIENT === 1){
                            return redirect(env('EXTERNAL_STORE_URL')."login");
                        }else{
                            return redirect(env('FRONT_URL')."login");
                        }
                    }
                }else{
                    if($request->EXTERNAL_CLIENT === 1){
                        return redirect(env('EXTERNAL_STORE_URL')."confirm-account/fail");
                    }else{
                        return redirect(env('FRONT_URL')."confirm-account/fail");
                    }
                }
            }
        }else{
            if($request->EXTERNAL_CLIENT === 1){
                return redirect(env('EXTERNAL_STORE_URL')."confirm-account/fail");
            }else{
                return redirect(env('FRONT_URL')."confirm-account/fail");
            }
        }
    }
    
    public function resendEmail($email)
    {
        $request = $this->rr->where('EMAIL', $email)->first();
        if ($request) {
            if ($request->SEND == 0 && $request->VERIFIED_EMAIL == 0) {

                if($request->EXTERNAL_CLIENT != 1){
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $charactersLength = strlen($characters);
                    $randomString = '';
                    for ($i = 0; $i < 13; $i++) {
                        $randomString .= $characters[Rand(0, $charactersLength - 1)];
                    }
                }

                $token = (new Crypto())->encrypt($request->ID, $request->EMAIL);
                if($request->EXTERNAL_CLIENT != 1){
                    $html = (new HtmlWriter($request->NAME ? $request->NAME : $request->SOCIAL_REASON))->validateDataEmail($token, $randomString);
                }else{
                    $html = (new HtmlWriter($request->NAME ? $request->NAME : $request->SOCIAL_REASON))->validateDataEmail($token);
                }

                $mg = new MailGunFactory();

                $email = explode('@', $request->EMAIL);

                if ($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com') {
                    $mail = Mail::to($request->EMAIL)->send(new RegistrationRequestMail($html));
                    $mail = true;
                } else {
                    $mail = $mg->send($request->EMAIL, 'Confirme seus dados', $html);
                }
                if($request->EXTERNAL_CLIENT != 1){
                    $sql = "UPDATE REGISTRATION_REQUEST
                                    SET
                                    DT_SEND = IF (SEND = 1, NOW() , NULL),
                                    SEND = 0,
                                    DT_RESEND  =  IF (RESEND = 1, NOW() , NULL),
                                    RESEND = 0,
                                    PASSWORD = sha2('{$randomString}', 256)
                                WHERE ID = {$request->ID} and EMAIL = '{$request->EMAIL}'";
                }else{
                    $sql = "UPDATE REGISTRATION_REQUEST
                                    SET
                                    DT_SEND = IF (SEND = 1, NOW() , NULL),
                                    SEND = 0,
                                    DT_RESEND  =  IF (RESEND = 1, NOW() , NULL),
                                    RESEND = 0
                                WHERE ID = {$request->ID} and EMAIL = '{$request->EMAIL}'";
                }

                SqlHelper::run($sql);

                if ($mail) {
                    return response()->json(['SUCCESS' => ['MESSAGE' => 'EMAIL RE-SENT']], 200);
                } else {
                    return (new Message())->defaultMessage(20, 500);
                }
            } else {
                return (new Message())->defaultMessage(17, 404);
            }
        }
    }

    public function search($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $json = (new MassiveJsonConverter())->generate('SEARCH', $request);
            $result = DB::select("CALL SP_SEARCH_REGISTRATION_REQUEST('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_REGISTRATION_REQUEST');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function pendingConfirmationRequests(Request $request)
    {

        Validator::make($request->all(), [
            'SPONSOR_ID' => 'required',
            'VERIFIED_EMAIL' => 'required'
        ])->validate();

        $user = UserAccount::find($request->SPONSOR_ID);
        if($user){
            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $json = (new MassiveJsonConverter())->generate('SEARCH', $request);
            $result = DB::select("CALL SP_SEARCH_REGISTRATION_REQUEST('{$json}', NULL, NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_REGISTRATION_REQUEST');
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function admUpdate($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $rr = $this->rr->find($request->ID);
        if(!$rr){
            return (new Message())->defaultMessage(7, 404);
        }

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){
            $json = (new MassiveJsonConverter())->generate('UPDATE', $request);
            $result = DB::select("CALL SP_UPDATE_REGISTRATION_REQUEST('{$json}', '{$uuid}', '', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_UPDATE_REGISTRATION_REQUEST');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
