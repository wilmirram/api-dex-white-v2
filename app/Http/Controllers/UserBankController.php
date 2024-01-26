<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserBankRequest;
use App\Models\User;
use App\Models\UserBank;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserBankController extends Controller
{
    private $userBank;

    public function __construct(UserBank $userBank)
    {
        $this->userBank = $userBank;
    }

    public function index()
    {
        $userBank = $this->userBank->all();
        return (new Message())->defaultMessage(21, 200, $userBank);
    }

    public function show($id)
    {
        $userBank = $this->userBank->find($id);
        if(!$userBank){
            return (new Message())->defaultMessage(17, 404);
        }else{
            return (new Message())->defaultMessage(1, 200, $userBank);
        }
    }

    public function store(UserBankRequest $request)
    {
        $user = User::find($request->USER_ID);
        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $data = $request->all();
            $userBank = $this->userBank->create($data);
            if($userBank){
                return (new Message())->defaultMessage(1, 200, $userBank);
            }else{
                return (new Message())->defaultMessage(20, 500);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }

    }

    public function update($id, Request $request)
    {
        $userBank = $this->userBank->find($id);
        if(!$userBank){
            return (new Message())->defaultMessage(17, 404);
        }

        foreach ($request->all() as $key => $value) {
            DB::select("UPDATE USER_BANK SET {$key} = '{$value}' WHERE id = {$id}");
        }

        return (new Message())->defaultMessage(22, 203);
    }

    public function setDescription($id, Request $request)
    {
        Validator::make($request->all(),[
            'DESCRIPTION' => 'required',
        ])->validate();

        $userBank = $this->userBank->find($id);

        if($userBank){
            $value = strtoupper($request->DESCRIPTION);
            DB::select("UPDATE USER_BANK SET DESCRIPTION = UPPER('{$value}') WHERE ID = {$id}");
            return (new Message())->defaultMessage(22, 200);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function setPreferentialBank($id, Request $request)
    {
        Validator::make($request->all(),[
            'PREFERENTIAL_BANK' => 'required',
        ])->validate();

        $preferentialBank = $this->userBank->find($request->PREFERENTIAL_BANK);
        if($preferentialBank){
            $user = User::find($id);
            if($user){
                if($preferentialBank->USER_ID == $id){
                    $banks = $this->userBank->where('USER_ID', $id)->get();
                    foreach ($banks as $bank){
                        if($bank->PREFERENTIAL_BANK === 1){
                            DB::select("UPDATE USER_BANK SET PREFERENTIAL_BANK = 0 WHERE ID = {$bank->ID}");
                        }
                    }
                    DB::select("UPDATE USER_BANK SET PREFERENTIAL_BANK = 1 WHERE ID = {$preferentialBank->ID}");
                    return (new Message())->defaultMessage(22, 200);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'THIS USER IS NOT THE OWNER OF THIS ACCOUNT']], 400);
                }
            }else{
                return (new Message())->defaultMessage(18, 404);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function getPreferentialBank($id)
    {
        $user = User::find($id);
        if($user){
            $bank = $this->userBank->where('USER_ID', $id)->where('PREFERENTIAL_BANK', 1)->first();
            if($bank != '' || $bank != null){
                return (new Message())->defaultMessage(1, 200, $bank);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => "THIS USER DOESN'T HAVE A PREFERENTIAL BANK"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function changeStatus($id)
    {
        $bank = $this->userBank->find($id);
        if($bank){
            if($bank->ACTIVE === 1){
                DB::select("UPDATE USER_BANK SET ACTIVE = 0 WHERE ID = {$bank->ID}");
                return response()->json(['SUCCESS' => ['MESSAGE' => "THE BANK WAS SUCCESSFULLY INACTIVATED"]], 200);
            }elseif ($bank->ACTIVE === 0){
                DB::select("UPDATE USER_BANK SET ACTIVE = 1 WHERE ID = {$bank->ID}");
                return response()->json(['SUCCESS' => ['MESSAGE' => "THE BANK WAS SUCCESSFULLY ACTIVED"]], 200);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => "INVALID VALUE"]], 500);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
