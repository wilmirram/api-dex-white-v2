<?php

namespace App\Http\Controllers;

use App\Mail\RecoveryAdmPasswordMail;
use App\Mail\RecoveryFinancialPassword;
use App\Mail\RecoveryPassword;
use App\Mail\RegistrationRequestMail;
use App\Models\Adm;
use App\Models\User;
use App\Utils\Crypto;
use App\Utils\HtmlWriter;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RecoveryPasswordController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function sendRecoveryPassword(Request $request)
    {
        Validator::make($request->all(), [
            'EMAIL' => 'required|email'
        ])->validate();

        $user = $this->user->where('EMAIL', $request->EMAIL)->first();
        if($user){
            $token = (new Crypto())->encrypt($user->ID, $user->EMAIL);
            $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->recoveryPassword($token);
            $mg = new MailGunFactory();

            $email = explode('@', $request->EMAIL);

            if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                $mail = Mail::to($request->EMAIL)->send(new RecoveryPassword($html));
                $mail = true;
            }else{
                $mail = $mg->send($request->EMAIL, 'Alteração de senha', $html);
            }

            if($mail){
                return response()->json(['SUCCESS' => ['MESSAGE' => 'INSTRUCTIONS FOR RECOVERING THE PASSWORD HAVE BEEN SENT TO YOUR EMAIL']]);
            }else{
                return (new Message())->defaultMessage(20, 500);
            }
        }else{
            return response()->json(['WARNING' => ['MESSAGE' => 'EMAIL HAS NOT BEEN FOUND IN OUR DATABASE']]);
        }
    }

    public function verifyRecoveryPassword($token)
    {
        $data = (new Crypto())->decrypt($token);
        if(!property_exists($data, 'id') || !property_exists($data, 'email') || !property_exists($data, 'exp')){
            return response()->json(['ERROR' => ['MESSAGE' => 'INCOMPLETE TOKEN']], 403);
        }
        $user = $this->user->find($data->id);
        if($user){
            if($user->EXERNAL_CLIENT == 1){
                //return redirect(env('FRONT_URL')."recovery-password/{$token}");
                return redirect(env('FRONT_URL')."recovery-password/{$token}");
            }else{
                return redirect(env('FRONT_URL')."recovery-password/{$token}");
            }
        }else{
            //return redirect(env('FRONT_URL')."recovery-password/{$token}");
            return redirect(env('FRONT_URL')."recovery-password/{$token}");
        }
    }

    public function setRecoveryPassword($id, Request $request)
    {
        Validator::make($request->all(), [
            'PASSWORD' => 'required',
            'EMAIL' => 'required|email'
        ])->validate();

        $user = $this->user->find($id);
        if($user){
            DB::select("UPDATE USER SET PASSWORD = sha2('{$request->PASSWORD}', 256) WHERE id = {$id} and EMAIL = '{$request->EMAIL}'");
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function sendRecoveryFinancialPassword(Request $request)
    {
        Validator::make($request->all(), [
            'EMAIL' => 'required|email'
        ])->validate();

        $user = $this->user->where('EMAIL', $request->EMAIL)->first();
        if($user){
            $token = (new Crypto())->encrypt($user->ID, $user->EMAIL);
            $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->recoveryFinancialPassword($token);
            $mg = new MailGunFactory();

            $email = explode('@', $request->EMAIL);

            if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                $mail = Mail::to($request->EMAIL)->send(new RecoveryFinancialPassword($html));
                $mail = true;
            }else{
                $mail = $mg->send($request->EMAIL, 'Alteração de senha financeira', $html);
            }

            if($mail){
                return response()->json(['SUCCESS' => ['MESSAGE' => 'INSTRUCTIONS FOR RECOVERING THE FINANCIAL PASSWORD HAVE BEEN SENT TO YOUR EMAIL']]);
            }else{
                return (new Message())->defaultMessage(20, 500);
            }
        }else{
            return response()->json(['WARNING' => ['MESSAGE' => 'EMAIL HAS NOT BEEN FOUND IN OUR DATABASE']]);
        }
    }

    public function verifyRecoveryFinancialPassword($token)
    {
        $data = (new Crypto())->decrypt($token);
        if(!property_exists($data, 'id') || !property_exists($data, 'email') || !property_exists($data, 'exp')){
            return response()->json(['ERROR' => ['MESSAGE' => 'INCOMPLETE TOKEN']], 403);
        }
        $user = $this->user->find($data->id);
        if($user){
            if($user->EXERNAL_CLIENT == 1){
                //return redirect(env('FRONT_URL')."recovery-password-financial/{$token}");
                return redirect(env('FRONT_URL')."recovery-password-financial/{$token}");
            }else{
                return redirect(env('FRONT_URL')."recovery-password-financial/{$token}");
            }
        }else{
            //return redirect(env('FRONT_URL')."recovery-password-financial/{$token}");
            return redirect(env('FRONT_URL')."recovery-password-financial/{$token}");
        }
    }

    public function setRecoveryFinancialPassword($id, Request $request)
    {
        Validator::make($request->all(), [
            'PASSWORD' => 'required',
            'EMAIL' => 'required|email',
            'token' => 'required'
        ])->validate();

        $verify = $this->verifyToken($request);
        if($verify->status() == 200){
            $user = $this->user->find($id);
            if($user){
                DB::select("UPDATE USER SET FINANCIAL_PASSWORD = sha2('{$request->PASSWORD}', 256) WHERE id = {$id} and EMAIL = '{$request->EMAIL}'");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(17, 404);
            }
        }else{
            return response()->json(['ERROR' => ['MESSAGE' => 'EXPIRED TOKEN']], 403);
        }
    }

    public function verifyToken(Request $request)
    {
        Validator::make($request->all(), [
            'token' => 'required',
        ])->validate();

        $data = (new Crypto())->decrypt($request->token);
        if(!property_exists($data, 'id') || !property_exists($data, 'email') || !property_exists($data, 'exp')){
            return response()->json(['ERROR' => ['MESSAGE' => 'INCOMPLETE TOKEN']], 403);
        }
        $user = $this->user->find($data->id);
        if($user){
            $token = new \DateTime($data->exp->date);
            $now = new \DateTime();
            $diff = date_diff($token, $now);
            if($diff->days >= 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'EXPIRED TOKEN']], 403);
            }else{
                return response()->json([
                    'Message' => 'THIS TOKEN IS VALID',
                    'User' => ['ID' => $user->ID, 'EMAIL' => $user->EMAIL]
                ], 200);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function sendRecoveryAdminPassword(Request $request)
    {
        Validator::make($request->all(), [
            'EMAIL' => 'required',
        ])->validate();

        $adm = Adm::where('EMAIL', $request->EMAIL)->first();

        if($adm){

            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';

            for ($i = 0; $i < 13; $i++) {
                $randomString .= $characters[Rand(0, $charactersLength - 1)];
            }

            $token = (new Crypto())->admEncrypt($adm->UUID, $randomString);

            $html = (new HtmlWriter($adm->NAME))->newAdmPassword($randomString, $token);
            $mg = new MailGunFactory();

            $email = explode('@', $request->EMAIL);

            if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                $mail = Mail::to($request->EMAIL)->send(new RecoveryAdmPasswordMail($html));
                $mail = true;
            }else{
                $mail = $mg->send($request->EMAIL, 'Nova Senha - Painel Administrador', $html);
            }

            if($mail){
                return response()->json(['SUCCESS' => ['MESSAGE' => 'INSTRUCTIONS FOR RECOVERING THE ADM PASSWORD HAVE BEEN SENT TO YOUR EMAIL']]);
            }else{
                return (new Message())->defaultMessage(20, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function verifyAdmToken($token)
    {
        $payload = (new Crypto())->admDecrypt($token);

        $adm = Adm::where('UUID', $payload->uuid)->first();
        if($adm){
            $token = new \DateTime($payload->exp->date);
            $now = new \DateTime();
            $diff = date_diff($token, $now);
            if($diff->days >= 1){
                return redirect("https://vg-admin.vg.company/login#tokenexpired");
            }else{
                DB::select("UPDATE ADM SET PASSWORD = sha2('{$payload->pssw}', 256) WHERE id = {$adm->ID}");
                return redirect("https://vg-admin.vg.company/login#passwordchangesuccess");
            }
        }else{
            return redirect("https://vg-admin.vg.company/login#tokeninvalid");
        }
    }

    public function newAdminPassword($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'OLD_PASSWORD' => 'required',
            'NEW_PASSWORD' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_AUTHENTICATE_LOGIN('{$adm->EMAIL}', '{$request->OLD_PASSWORD}', 2, NULL, @P_REF_ID)");
            if($result[0]->CODE == 1){
                DB::select("UPDATE ADM SET PASSWORD = sha2('{$request->NEW_PASSWORD}', 256) WHERE id = {$adm->ID}");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => 'INVALID OLD PASSWORD']], 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
