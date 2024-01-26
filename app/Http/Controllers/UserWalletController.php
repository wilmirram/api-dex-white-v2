<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserWalletRequest;
use App\Http\Requests\cryptoWalletEncrypt;
use App\Mail\NewWalletMail;
use App\Models\User;
use App\Utils\Crypto;
use App\Models\UserAccount;
use App\Models\UserWallet;
use App\Utils\JwtValidation;
use App\Utils\HtmlWriter;
use App\Utils\MailGunFactory;
use App\Utils\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class UserWalletController extends Controller
{
    private $userWallet;

    public function __construct(UserWallet $userWallet)
    {
        $this->userWallet = $userWallet;
    }

    public function index()
    {
        $wallets = $this->userWallet->all();
        return (new Message())->defaultMessage(1, 200, $wallets);
    }

    public function show($id)
    {
        $wallet = $this->userWallet->find($id);
        if(!$wallet){
            return (new Message())->defaultMessage(17, 404);
        }else{
            return response()->json($wallet);
        }
    }

    public function store2(UserWalletRequest $request)
    {
        $data = $request->all();
        $user = User::find($request->USER_ID);
        //$useraccount = $request->P_USER_ACCOUNT_ID;
        if($user){
        
            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("SELECT FN_VERIFY_FINANCIAL_PASSWORD_WALLET('{$request->USER_ID}','{$request->FINANCIAL_PASSWORD}') AS fp ");
            
            if($result[0]->fp != 1){
                return (new Message())->defaultMessage(37, 403);
            }
            
            //$testeuser = $request->P_USER_ID;

            // inserir a nova carteira no banco
            $result = DB::select("CALL SP_NEW_WALLET_REGISTRATION({$request->USER_ID},
                                                                 '{$request->CRYPTO_CURRENCY_ID}',
                                                                 '{$request->ADDRESS}',
                                                                 '{$request->EXCHANGE_ID}',
                                                                 '{$request->FINANCIAL_PASSWORD}')");

            if($result[0]->CODE == 1){

                // gerar token  
                $token = (new Crypto())->cryptoWalletEncrypt($request->USER_ID, $request->ADDRESS);
                
                // enviar e-mail
                $mg = new MailGunFactory();

                $eUser = User::find($user->ID);
                $email = explode('@', $user->EMAIL);

                $html = (new HtmlWriter($eUser->NAME ? $eUser->NAME : $eUser->SOCIAL_REASON))->newWalletRequest($token, $request->ADDRESS);

                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                    $mail = Mail::to($eUser->EMAIL)->send(new NewWalletMail($html));
                    $mail = true;
                }else{
                    $mail = $mg->send($eUser->EMAIL, 'Nova Carteira Cadastrada!', $html);
                }

                if($mail){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'THERE WAS AN ERROR SENDING EMAIL']], 400);
                }

                // nova função para receber a confirmação

                //$wallet = $this->userWallet->create($data);
                
            }
            
        }
    }

    public function walletRequest($token)
    {
        $token = (new Crypto())->cryptoWalletDescrypt($token);
        
        $teste = $token->uid;
        
        //$user = UserAccount::find($token->uid); //BUSCA O USER ACCOUNT
        
        $fp = User::find($teste);
        
        if($fp != ''){
            $exp = new \DateTime($token->exp->date);
            $now = new \DateTime();
            $diff = date_diff($exp, $now);
            if($diff->days >= 1){
                return redirect(env('FRONT_URL')."login#withdrawfail");
            }else{
                
                $result = DB::select("CALL SP_CONFIRM_NEW_WALLET('{$fp->EMAIL}', {$fp->ID}, '{$token->uwi}')");
             
                if($result[0]->CODE == 1){
                    return redirect(env('FRONT_URL')."login#walletsuccess");
                } elseif ($result[0]->CODE == 37){
                    return redirect(env('FRONT_URL')."login#walletfail");
                }else{
                    return redirect(env('FRONT_URL')."login#walletfail");
                }
            }

            
            
        }else{
            return redirect(env('FRONT_URL')."login#walletfail");
        }
        
    }

    public function update($id, Request $request)
    {
        $wallet = $this->userWallet->find($id);
        if(!$wallet){
            return (new Message())->defaultMessage(17, 404);
        }

        foreach ($request->all() as $key => $value) {
            DB::select("UPDATE USER_WALLET SET {$key} = '{$value}' WHERE id = {$id}");
        }
        return (new Message())->defaultMessage(22, 203);
    }

    public function setDescription($id, Request $request)
    {
        Validator::make($request->all(),[
            'DESCRIPTION' => 'required',
        ])->validate();

        $wallet = $this->userWallet->find($id);

        if($wallet){
            $value = strtoupper($request->DESCRIPTION);
            DB::select("UPDATE USER_WALLET SET DESCRIPTION = UPPER('{$value}') WHERE ID = {$id}");
            return (new Message())->defaultMessage(22, 200);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function setPreferentialWallet($id, Request $request)
    {
        Validator::make($request->all(), [
            'PREFERENTIAL_WALLET' => 'required',
        ])->validate();

        $preferentialWallet = $this->userWallet->find($request->PREFERENTIAL_WALLET);
        if ($preferentialWallet) {
            $user = User::find($id);
            if ($user) {
                if ($preferentialWallet->USER_ID == $id) {
                    $wallets = $this->userWallet->where('USER_ID', $id)->get();
                    foreach ($wallets as $wallet) {
                        if ($wallet->PREFERENTIAL_WALLET === 1) {
                            DB::select("UPDATE USER_WALLET SET PREFERENTIAL_WALLET = 0 WHERE ID = {$wallet->ID}");
                        }
                    }
                    DB::select("UPDATE USER_WALLET SET PREFERENTIAL_WALLET = 1 WHERE ID = {$preferentialWallet->ID}");
                    return (new Message())->defaultMessage(22, 200);
                } else {
                    return response()->json(['ERROR' => ['MESSAGE' => 'THIS USER IS NOT THE OWNER OF THIS WALLET']], 400);
                }
            } else {
                return (new Message())->defaultMessage(18, 404);
            }
        } else {
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function getPreferentialWallet($id)
    {
        $user = User::find($id);
        if($user){
            $wallet = $this->userWallet->where('USER_ID', $id)->where('PREFERENTIAL_WALLET', 1)->first();
            if($wallet != '' || $wallet != null){
                return (new Message())->defaultMessage(1, 200, $wallet);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => "THIS USER DOESN'T HAVE A PREFERENTIAL WALLET"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function changeStatus($id)
    {
        $wallet = $this->userWallet->find($id);
        if($wallet){
            if($wallet->ACTIVE === 1){
                DB::select("UPDATE USER_WALLET SET ACTIVE = 0 WHERE ID = {$wallet->ID}");
                return response()->json(['SUCCESS' => ['MESSAGE' => "THE WALLET WAS SUCCESSFULLY INACTIVATED"]], 200);
            }elseif ($wallet->ACTIVE === 0){
                DB::select("UPDATE USER_WALLET SET ACTIVE = 1 WHERE ID = {$wallet->ID}");
                return response()->json(['SUCCESS' => ['MESSAGE' => "THE WALLET WAS SUCCESSFULLY ACTIVED"]], 200);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => "INVALID VALUE"]], 500);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
