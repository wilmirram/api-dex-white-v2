<?php

namespace App\Http\Controllers;

use App\Models\OrderTracking;
use App\Models\UserAccount;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderTrackingController extends Controller
{
    private $order;

    public function __construct(OrderTracking $order)
    {
        $this->order = $order;
    }

    public function show($id, Request $request)
    {
        $user = UserAccount::find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_ORDER_TRACKING_LIST('{$id}')");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }
}
