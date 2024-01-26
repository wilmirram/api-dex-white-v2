<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Models\WithdrawlMethod;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawlMethodController extends Controller
{
    private $withdrawl;

    public function __construct(WithdrawlMethod $withdrawl)
    {
        $this->withdrawl = $withdrawl;
    }

    public function index()
    {
        $withdrawl = $this->withdrawl->where('ACTIVE', 1)->get();
        return (new Message())->defaultMessage(1, 200, $withdrawl);
    }

    public function enableWithdrawlScreen($id, Request $request)
    {
        $user = UserAccount::find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("SELECT FN_GET_ENABLE_WITHDRAWAL_SCREEN({$id}) as result");
            if($result[0]->result === 1){
                return (new Message())->defaultMessage(1, 200, $result[0]->result);
            }else{
                return (new Message())->defaultMessage($result[0]->result, 403, $result[0]->result);
            }

        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }
}
