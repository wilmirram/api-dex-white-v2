<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\User;
use App\Models\UserAccount;
use App\Utils\Crypto;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class JWTAuthenticateController extends Controller
{

    public function login(Request $request)
    {
        Validator::make($request->all(), [
            'EMAIL' => 'required|email',
            'PASSWORD' => 'required',
            'P_SYSTEM_ID' => 'required',
            'P_IP' => 'required'
        ])->validate();

        $result = DB::select("CALL SP_AUTHENTICATE_LOGIN('{$request->EMAIL}', '{$request->PASSWORD}', {$request->P_SYSTEM_ID}, NULL, @P_REF_ID)");
        if($result[0]->CODE == 1){
            $id = DB::select("SELECT @P_REF_ID as ref")[0]->ref;
            $user = User::find($id);
            
            if($user->EXTERNAL_CLIENT != 1){
                $account = UserAccount::where('USER_ID', $user->ID)->get(['ID', 'NICKNAME']);
            }
            
            if($user){
                
                if($request->P_SYSTEM_ID == 1){
                    $signer = new Sha256();
                    $time = time();
                    $token = (new Builder())
                        ->withClaim('uid', $user->ID)
                        ->expiresAt($time + 50000)
                        ->getToken($signer, new Key(env('JWT_SECRET')));
                }else{
                    $signer = new Sha256();
                    $time = time();
                    $token = (new Builder())
                        ->withClaim('uid', $user->ID)
                        ->expiresAt($time + 1800)
                        ->getToken($signer, new Key(env('JWT_SECRET')));

                    
                }

                $vdu = 1;

                if($user->TYPE_DOCUMENT_ID == 1 || $user->TYPE_DOCUMENT_ID == 3){
                    if(is_null($user->DT_BIRTHDAY) || is_null($user->COUNTRY_ID) || is_null($user->PHONE)){
                        $vdu = 0;
                    }
                }else{
                    if(is_null($user->DT_BIRTHDAY) || is_null($user->SOCIAL_REASON) || is_null($user->FANTASY_NAME) || is_null($user->COUNTRY_ID) || is_null($user->PHONE)){
                        $vdu = 0;
                    }
                }

                if($request->P_SYSTEM_ID == 1){
                    if($user->EXTERNAL_CLIENT == 1){
                        $data = [
                            'Token' => (string) $token,
                            'Verified_Data_User' => $vdu,
                            'System' => (int) $request->P_SYSTEM_ID,
                            'User' => $user->makeHidden(['PASSWORD', 'FINANCIAL_PASSWORD', 'ACCEPTED_TERM']),
                        ];
                    }else{
                        $career = 0;
                        if($user->CAREER_PATH_USER_ACCOUNT_ID != null) $career = 1;
                        $data = [
                            'Token' => (string) $token,
                            'Verified_Data_User' => $vdu,
                            'career_path' => $career,
                            'System' => (int) $request->P_SYSTEM_ID,
                            'User' => $user->makeHidden(['PASSWORD', 'FINANCIAL_PASSWORD', 'ACCEPTED_TERM']),
                            'UserAccounts' => [
                                $account
                            ]
                        ];
                    }
                }else{
                    $data = [
                        'Token' => (string) $token,
                        'System' => (int) $request->P_SYSTEM_ID,
                    ];
                }

                $adm = Adm::find($user->ID);
                if($adm && $request->P_SYSTEM_ID == 2){
                    $adm_Menu = DB::select("CALL SP_GET_ADM_MENU('{$adm->UUID}')");
                    $data += [
                        'Adm' => $adm->makeHidden(['PASSWORD']),
                        'Permission' => "{".$adm_Menu[0]->PERMISSION."}"
                    ];
                }

                return response()->json($data, 200);
                
            }else{
                return (new Message())->defaultMessage(18, 400);
            
            }
        }else{
            return (new Message())->defaultMessage(10, 400);
        }
    }

    public function accessToken($time)
    {
        $body = Crypto::accessTokenEncrypt((int) $time * 60);
        return (new Message())->defaultMessage(1, 200, ['token' => $body]);
    }

    public function validateAccessToken($token)
    {
        if (empty($token) || $token == '') return response()->json(['ERROR' => ['MESSAGE' => 'INVALID TOKEN']], 403);
        $token = Crypto::accessTokenDecrypt($token);

        if (! $token) return response()->json(['ERROR' => ['MESSAGE' => 'EXPIRED TOKEN']], 403);
        return (new Message())->defaultMessage(1, 200);
    }

    public function refreshToken(Request $request)
    {
        $token = $request->header('Authorization');
        $token = explode(' ', $token);
        $token = explode('.', $token[1]);
        $payload = $token[1];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);

        $user = User::find($payload->uid);
        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $signer = new Sha256();
            $time = time();
            $token = (new Builder())
                ->withClaim('uid', $payload->uid)
                ->expiresAt($time + 600)
                ->getToken($signer, new Key(env('JWT_SECRET')));

            $token = (string) $token;
            return (new Message())->defaultMessage(1, 200, $token);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function refreshAdmToken(Request $request)
    {
        $token = $request->header('Authorization');
        $token = explode(' ', $token);
        $token = explode('.', $token[1]);
        $payload = $token[1];
        $payload = base64_decode($payload);
        $payload = json_decode($payload);

        $adm = Adm::find($payload->uid);
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $signer = new Sha256();
            $time = time();
            $token = (new Builder())
                ->withClaim('uid', $payload->uid)
                ->expiresAt($time + 1800)
                ->getToken($signer, new Key(env('JWT_SECRET')));

            $token = (string) $token;
            return (new Message())->defaultMessage(1, 200, $token);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
