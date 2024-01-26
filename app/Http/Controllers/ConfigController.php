<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Config;
use App\Models\UserAccount;
use App\Utils\JwtValidation;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\json_decode;

class ConfigController extends Controller
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function search($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_SEARCH_CONFIG('{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_CONFIG');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function admUpdate($uuid, Request $request)
    {
        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $json = (new MassiveJsonConverter())->generate('UPDATE', $request);
            $result = DB::select("CALL SP_UPDATE_CONFIG('{$json}', '{$uuid}', '', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_UPDATE_CONFIG');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function admChangePreferentialSponsor($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('ID', $request->P_USER_ACCOUNT_ID)->first();
            if($userAccount){
                DB::select("UPDATE CONFIG SET PREFERENTIAL_SPONSOR_ID = {$userAccount->ID}");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getPreferentialSponsor()
    {
        $sponsor = $this->config->get(['PREFERENTIAL_SPONSOR_ID']);
        $sponsor = (json_decode($sponsor));
        $nickname = UserAccount::where('ID', $sponsor[0]->PREFERENTIAL_SPONSOR_ID)->get(['NICKNAME']);
        return (new Message())->defaultMessage(1, 200, $nickname);
    }

    public function getWithdrawalConfigs($uuid, Request $request)
    {
        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $configs = $this->config->get(['WITHDRAWAL_AMOUNT_MIN', 'WITHDRAWAL_AMOUNT_MAX', 'WITHDRAWAL_AMOUNT_CRYPTO_MIN', 'WITHDRAWAL_AMOUNT_CRYPTO_MAX']);
            return (new Message())->defaultMessage(1, 200, $configs);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function updateWithdrawalConfigs($uuid, Request $request)
    {
        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            if((new JwtValidation())->validateByUser($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            if($request->has('WITHDRAWAL_AMOUNT_MIN')){
                DB::select("UPDATE CONFIG SET WITHDRAWAL_AMOUNT_MIN = {$request->WITHDRAWAL_AMOUNT_MIN} WHERE ID = 1");
            }

            if($request->has('WITHDRAWAL_AMOUNT_MAX')){
                DB::select("UPDATE CONFIG SET WITHDRAWAL_AMOUNT_MAX = {$request->WITHDRAWAL_AMOUNT_MAX} WHERE ID = 1");
            }

            if($request->has('WITHDRAWAL_AMOUNT_CRYPTO_MIN')){
                DB::select("UPDATE CONFIG SET WITHDRAWAL_AMOUNT_CRYPTO_MIN = {$request->WITHDRAWAL_AMOUNT_CRYPTO_MIN} WHERE ID = 1");
            }

            if($request->has('WITHDRAWAL_AMOUNT_CRYPTO_MAX')){
                DB::select("UPDATE CONFIG SET WITHDRAWAL_AMOUNT_CRYPTO_MAX = {$request->WITHDRAWAL_AMOUNT_CRYPTO_MAX} WHERE ID = 1");
            }

            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getWithdrawalLimit(Request $request)
    {
        Validator::make($request->all(), [
            'P_WITHDRAWAL_METHOD_ID' => 'required'
        ])->validate();

        $result = DB::select("CALL SP_WITHDRAWAL_LIMITS({$request->P_WITHDRAWAL_METHOD_ID})");
        return (new Message())->defaultMessage(1, 200, $result[0]);
    }
}
