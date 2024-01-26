<?php

namespace App\Http\Controllers\Market;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationRequestMail;
use App\Models\RegistrationRequest;
use App\Models\UserAccount;
use App\Utils\Crypto;
use App\Utils\HtmlWriter;
use App\Utils\MailGunFactory;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use App\Utils\SqlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegistrationRequestController extends Controller
{
    private $rr;

    public function __construct(RegistrationRequest $rr)
    {
        date_default_timezone_set ( 'America/Sao_Paulo');
        $this->rr = $rr;
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'EMAIL' => 'required',
            'PASSWORD' => 'required',
            'TYPE_DOCUMENT_ID' => 'required',
            'DOCUMENT' => 'required'
        ])->validate();

        if(!$request->has('EXTERNAL_CLIENT')){
            $request['EXTERNAL_CLIENT'] = "1";
        }

        $query = "SELECT FN_EXISTING_EMAIL('{$request->EMAIL}') as email,
                  FN_REGISTERED_USER('{$request->EMAIL}') as user,
                  FN_EXISTING_DOCUMENT('{$request->DOCUMENT}') as document,
                  FN_EXISTING_NICKNAME('{$request->NICKNAME}', NULL) as nickname";
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
        $flag = 0;
        $data = ['OPERATION' => "INSERT"];
        $data += $request->all();
        if ($request->has('SPONSOR_NICKNAME')){
            if ($request->SPONSOR_NICKNAME == 'NULL'){
                $flag = 1;
                $data += ['SPONSOR_ID' => NULL];
            }else{
                $sponsor = UserAccount::where('NICKNAME', $request->SPONSOR_NICKNAME)->first();
                if (!$sponsor) return (new Message())->defaultMessage(17, 404);
                $data += ['SPONSOR_ID' => (string) $sponsor->ID];
            }
            unset($data['SPONSOR_NICKNAME']);
        }

        //$json = ((new MassiveJsonConverter())->generate("INSERT", $request));
        if ($flag == 1){
            $json = json_encode($data);
        }else{
            $json = MassiveJsonConverter::generateGenericJson($data);
        }
        
        $query = "CALL SP_NEW_REGISTRATION_REQUEST_JSON('{$json}')";
        
        $result = SqlHelper::execParamQuery($query, "SELECT @ID as ID");
        
        
        
        if($result['result']['CODE'] == 1){
            $requestUser = $this->rr->find($result['result']['LAST_INSERT_ID']);
            $token = (new Crypto())->encrypt($requestUser->ID, $requestUser->EMAIL);
            $html = (new HtmlWriter($requestUser->NAME ? $requestUser->NAME : $requestUser->SOCIAL_REASON))->validateDataEmailExternal($token);
            $mg = new MailGunFactory();
            $sql = "UPDATE REGISTRATION_REQUEST
                            SET
                            DT_SEND = IF (SEND = 1, NOW() , NULL),
                            SEND = 0,
                            DT_RESEND  =  IF (RESEND = 1, NOW() , NULL),
                            RESEND = 0
                        WHERE ID = {$requestUser->ID} and EMAIL = '{$requestUser->EMAIL}'";
            SqlHelper::run($sql);

            $email = explode('@', $request->EMAIL);
            if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                $mail = Mail::to($request->EMAIL)->send(new RegistrationRequestMail($html));
                $mail = true;
            }else{
                $mail = $mg->send($requestUser->EMAIL, 'Confirme seus dados', $html);
            }

            if($mail){
                $date = date('Y-m-d H:i:s');
                $sql = "UPDATE REGISTRATION_REQUEST SET SEND = '0', DT_SEND = '{$date}' WHERE id = {$requestUser->ID}";
                SqlHelper::run($sql);
                return response()->json(['SUCCESS' => ['MESSAGE' => 'INSTRUCTIONS TO COMPLETE YOUR REGISTRATION WERE SENT TO YOUR EMAIL']]);
            }else{
                return (new Message())->defaultMessage(20, 400);
            }
        }else{
            return (new Message())->defaultMessage(20, 400);
        }
    }
}
