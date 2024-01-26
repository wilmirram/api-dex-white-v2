<?php

namespace App\Http\Controllers;

use App\Models\OrderTracking;
use App\Models\OrderTrackingItem;
use App\Models\UserAccount;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderTrackingItemController extends Controller
{
    private $order;

    public function __construct(OrderTrackingItem $order)
    {
        $this->order = $order;
    }

    public function show(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_ORDER_TRACKING_ID' => 'required'
        ])->validate();

        $user = UserAccount::find($request->P_USER_ACCOUNT_ID);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $order = OrderTracking::find($request->P_ORDER_TRACKING_ID);
            if($order){
                $result = DB::select("CALL SP_GET_ORDER_TRACKING_ITEM_LIST('{$request->P_USER_ACCOUNT_ID}', '{$request->P_ORDER_TRACKING_ID}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(28, 404);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }
}
