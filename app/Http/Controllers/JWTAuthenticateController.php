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
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use function bin2hex;
use function random_bytes;

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
                    //$signer = new Sha256();
                    $time = new DateTimeImmutable();
                    $token = (new Builder())
                        ->identifiedBy('uid', $user->ID)
                        //->identifiedBy(bin2hex(random_bytes(16)))
                        ->issuedAt($time)
                        ->expiresAt($time->modify('+0 minutes'))
                        //->getToken($signer, new Key(env('JWT_SECRET')));
                        ->getToken($this->Configuration->signer(), new Key(env('JWT_SECRET')));
                }else{
                    $signer = new Sha256();
                    $time = new DateTimeImmutable();
                    $token = (new Builder())
                        ->withClaim('uid', $user->ID)
                        //->expiresAt($time->modify('+10 minutes'))
                        ->issuedAt($time)
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
}
