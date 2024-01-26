<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyCashBackRequest;
use App\Models\Adm;
use App\Models\DailyCashBack;
use App\Utils\JwtValidation;
use App\Utils\Message;
use App\Utils\SqlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyCashBackController extends Controller
{
    private $cash;

    public function __construct(DailyCashBack $cash)
    {
        $this->cash = $cash;
    }

    public function insertCashBack(DailyCashBackRequest $request)
    {
        $adm = Adm::where('UUID', $request->P_ADM_UUID)->first();
        if($adm){
            $query = "CALL SP_INSERT_CASHBACK('{$request->P_DT_REFERENCE}', {$request->P_TICKET}, '{$request->P_ADM_UUID}')";
            $result = SqlHelper::exec($query);
            if($result['CODE'] == 1){
                return (new Message())->defaultMessage($result['CODE'], 200, $result);
            }else{
                return (new Message())->defaultMessage($result['CODE'], 400, null, 'SP_INSERT_CASHBACK');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function reportInsertCashback(DailyCashBackRequest $request)
    {
        $adm = Adm::where('UUID', $request->P_ADM_UUID)->first();
        if($adm){
            $query = "CALL SP_REPORT_INSERT_CASHBACK('{$request->P_DT_REFERENCE}', {$request->P_TICKET}, '{$request->P_ADM_UUID}')";
            $result = SqlHelper::exec($query);
            if($result['CODE'] == 1){
                return (new Message())->defaultMessage($result['CODE'], 200, $result);
            }else{
                return (new Message())->defaultMessage($result['CODE'], 400, null, 'SP_REPORT_INSERT_CASHBACK');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getDailyCashBackList($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_DAILY_CASHBACK_LIST('{$uuid}', @CODE_LIST_ID)");
            $code = DB::select("SELECT @CODE_LIST_ID as CODE");
            if($code[0]->CODE == 1){
                return (new Message())->defaultMessage($code[0]->CODE, 200, $result);
            }else{
                return (new Message())->defaultMessage($code[0]->CODE, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
