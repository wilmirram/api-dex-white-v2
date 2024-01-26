<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyBonusScoreRequest;
use App\Models\Adm;
use App\Models\DailyBonusScore;
use App\Utils\JwtValidation;
use App\Utils\Message;
use App\Utils\SqlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyBonusScoreController extends Controller
{
    private $bonus;

    public function __construct(DailyBonusScore $bonus)
    {
        $this->bonus = $bonus;
    }

    public function dailyBonusScore(DailyBonusScoreRequest $request)
    {
        $query = "CALL SP_INSERT_BONUS_SCORE('{$request->P_DT_REFERENCE}', '{$request->P_ADM_UUID}')";
        $result = SqlHelper::exec($query);
        if($result['CODE'] == 1){
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage($result['CODE'], 400, null, 'SP_INSERT_BONUS_SCORE');
        }
    }

    public function reportInsertBonusScore(DailyBonusScoreRequest $request)
    {
        $query = "CALL SP_REPORT_INSERT_BONUS_SCORE('{$request->P_DT_REFERENCE}', '{$request->P_ADM_UUID}')";
        $result = SqlHelper::exec($query);
        if($result['CODE'] == 1){
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage($result['CODE'], 400, null, 'SP_REPORT_INSERT_BONUS_SCORE');
        }
    }

    public function getDailyBonusScoreList($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_DAILY_BONUS_SCORE_LIST('{$uuid}', @CODE_LIST_ID)");
            $code = DB::select("SELECT @CODE_LIST_ID as CODE");
            if($code[0]->CODE == 1){
                return (new Message())->defaultMessage($code[0]->CODE, 200, $result);
            }else{
                return (new Message())->defaultMessage($code[0]->CODE, 400, null, 'SP_GET_DAILY_BONUS_SCORE_LIST');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
