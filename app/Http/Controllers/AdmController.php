<?php

namespace App\Http\Controllers;

use App\Mail\NewUserEmailMail;
use App\Models\AccessLevel;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationRequestMail;
use App\Models\Adm;
use App\Models\UserAccount;
use App\Models\UserBank;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\WithDrawlRequest;
use App\Utils\Crypto;
use App\Utils\HtmlWriter;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use function GuzzleHttp\json_encode;

class AdmController extends Controller
{
    private $adm;

    public function __construct(Adm $adm)
    {
        $this->adm = $adm;
    }

    public function getAdmPrivileges($uuid)
    {
        $adm = $this->adm->where('UUID', $uuid)->first();
        if($adm){
            $result = DB::select("CALL SP_GET_ADM_PRIVILEGES('{$uuid}')");
            if($result != null || $result != ''){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(17, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function blockedCashback($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'nickname' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->nickname)->first();

            if($userAccount){
                $result = DB::select("CALL SP_BLOCKED_CASHBACK_LIST({$userAccount->ID})");
                $start = (DB::select("SELECT FN_DT_START_BLOCKING_PERIOD({$userAccount->ID}) as result"))[0]->result;
                $end = (DB::select("SELECT FN_DT_END_BLOCKING_PERIOD({$userAccount->ID}) as result"))[0]->result;
                $data = ["List" => $result,
                    "Start" => $start,
                    "End" => $end];
                return (new Message())->defaultMessage(1, 200, $data);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }

    }

    public function searchReport($uuid, Request $request)
    {
        $adm = $this->adm->where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $data = $request->all();

        $data['OPERATION'] = 'SEARCH';

        foreach ($data as $key => $value){
            if ($value == '' || $value == 'null' || $value == 'NULL' || $value == null){
                unset($data[$key]);
            }
        }

        $json = json_encode($data);
        $result = DB::select("CALL SP_SEARCH_REPORT_VS_ORDER('{$json}', '{$uuid}', @P_CODE_LIST_ID)");
        $code_list = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;

        if ($code_list == 39) return (new Message())->defaultMessage(39, 400, '', 'SP_SEARCH_REPORT_VS_ORDER');

        if ($code_list != 1) return (new Message())->defaultMessage($code_list, 400);

        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function getUserAccountInformationAll($uuid, Request $request)
    {

        Validator::make($request->all(), [
            'nickname' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->nickname)->first();
            if($userAccount){
                $uaInformation = DB::select("CALL SP_GET_USER_ACCOUNT_INFORMATION({$userAccount->ID})");
                $userAccounts = DB::select("CALL SP_GET_USER_ACCOUNT({$userAccount->USER_ID})");
                $cashBackWeek = DB::select("CALL SP_GET_UA_CASHBACK_WEEK_VALIDATE_LIST({$userAccount->ID})");
                $result = ['UserAccountInformation' => $uaInformation,
                    'UserAccounts' => $userAccounts,
                    'CashBackValidateWeek' => $cashBackWeek];
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getBinaryChartList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
            'P_DAYS'=> 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_BINARY_CHART_LIST({$userAccount->ID}, {$request->P_DAYS})");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getUserData($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $data = User::find($userAccount->USER_ID);
                return (new Message())->defaultMessage(1, 200, $data->makeHidden(['PASSWORD', 'FINANCIAL_PASSWORD', 'ACCEPTED_TERM']));
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function manuallyEnterUserAccountScore($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_SIDE' => 'required',
            'P_SCORE' => 'required',
            'P_NOTE' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();
        if (!$adm) return (new Message())->defaultMessage(27, 404);

        $result = DB::select("CALL SP_MANUALLY_ENTER_USER_ACCOUNT_SCORE(
                                       {$request->P_USER_ACCOUNT_ID},
                                       {$request->P_SIDE},
                                       {$request->P_SCORE},
                                       '{$request->P_NOTE}',
                                       '{$uuid}'
       )");
        if ($result[0]->CODE != 1) return (new Message())->defaultMessage($result[0]->CODE, 400);
        return (new Message())->defaultMessage(1, 200);
    }

    public function getUserBank($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_USER_BANK('{$userAccount->USER_ID}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getPreferentialBank($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $bank = UserBank::where('USER_ID', $userAccount->USER_ID)->where('PREFERENTIAL_BANK', 1)->first();
                if($bank != '' || $bank != null){
                    return (new Message())->defaultMessage(1, 200, $bank);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => "THIS USER DOESN'T HAVE A PREFERENTIAL BANK"]], 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getUserWallet($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_USER_WALLET('{$userAccount->USER_ID}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getPreferentialUserWallet($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $wallet = UserWallet::where('USER_ID', $userAccount->USER_ID)->where('PREFERENTIAL_WALLET', 1)->first();
                if($wallet != '' || $wallet != null){
                    return (new Message())->defaultMessage(1, 200, $wallet);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => "THIS USER DOESN'T HAVE A PREFERENTIAL WALLET"]], 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getProductList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_PRODUCT_LIST('{$userAccount->ID}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getOrderItemList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_ORDER_ITEM_LIST({$userAccount->ID})");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getNetworkList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
            'P_REF_USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_NETWORK_LIST({$userAccount->ID}, {$request->P_REF_USER_ACCOUNT_ID})");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getNetworkListByNickname($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'USER_ACCOUNT_ID' => 'required',
            'P_REF_NICKNAME' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            $userAccount = UserAccount::find($request->USER_ACCOUNT_ID);
            if($userAccount){
                $id = (DB::select("SELECT FN_GET_DOWNLINE_BY_NICKNAME({$request->USER_ACCOUNT_ID}, '{$request->P_REF_NICKNAME}') AS result"))[0]->result;
                if($id != 0){
                    $result = DB::select("CALL SP_GET_NETWORK_LIST({$userAccount->ID}, {$id})");
                    return (new Message())->defaultMessage(1, 200, $result);
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "NICKNAME DOES NOT BELONG TO THIS NETWORK"]], 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getSponsorsList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_SPONSORS_LIST('{$userAccount->ID}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getStatementList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
            'P_DATE' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_ACCOUNT_STATEMENT_LIST('{$userAccount->ID}', '{$request->P_DATE}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function orderTracking($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $result = DB::select("CALL SP_GET_ORDER_TRACKING_LIST('{$userAccount->ID}')");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function UserAccountScoreList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
            'DATE' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $leftData = DB::select("CALL SP_GET_USER_ACCOUNT_SCORE_LIST({$userAccount->ID}, '{$request->DATE}', 1)");

                if(empty($leftData)){
                    $leftData = NULL;
                }
                $leftScore = DB::select("SELECT FN_GET_SCORE_LEG({$userAccount->ID},1) as result")[0]->result;
                $left = ["List" => $leftData, "Score" => $leftScore];

                $rightData = DB::select("CALL SP_GET_USER_ACCOUNT_SCORE_LIST({$userAccount->ID}, '{$request->DATE}', 2)");
                if(empty($rightData)){
                    $rightData = NULL;
                }
                $rightScore = DB::select("SELECT FN_GET_SCORE_LEG({$userAccount->ID},2) as result")[0]->result;
                $right = ["List" => $rightData, "Score" => $rightScore];

                $data = ['Left' => $left, 'Right' => $right];

                return (new Message())->defaultMessage(1, 200, $data);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function vsUserAccountScoreList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
            'DATE' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm){

            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if($userAccount){
                $leftData = DB::select("CALL SP_GET_VS_USER_ACCOUNT_SCORE_LIST({$userAccount->ID}, '{$request->DATE}', 1)");

                if(empty($leftData)){
                    $leftData = NULL;
                }
                //$leftScore = DB::select("SELECT FN_GET_VS_SCORE_LEG({$userAccount->ID},1) as result")[0]->result;
                $leftScore = 0;
                $left = ["List" => $leftData, "Score" => $leftScore];

                $rightData = DB::select("CALL SP_GET_VS_USER_ACCOUNT_SCORE_LIST({$userAccount->ID}, '{$request->DATE}', 2)");
                if(empty($rightData)){
                    $rightData = NULL;
                }
                //$rightScore = DB::select("SELECT FN_GET_VS_SCORE_LEG({$userAccount->ID},2) as result")[0]->result;
                $rightScore = 0;
                $right = ["List" => $rightData, "Score" => $rightScore];

                $data = ['Left' => $left, 'Right' => $right];

                return (new Message())->defaultMessage(1, 200, $data);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getLastNetworkLeg($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
            'P_SIDE' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if ($userAccount) {
                $result = DB::select("SELECT FN_GET_LAST_NETWORK_LEG({$userAccount->ID}, {$request->P_SIDE}) as UserAccountId");
                return (new Message())->defaultMessage(1, 200, $result[0]);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function orderTrackingItem($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NICKNAME' => 'required',
            'P_ORDER_TRACKING_ID' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();

            if ($userAccount) {
                $result = DB::select("CALL SP_GET_ORDER_TRACKING_ITEM_LIST({$userAccount->ID}, {$request->P_ORDER_TRACKING_ID})");
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getAdmMenu($uuid, Request $request)
    {
        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_ADM_MENU('{$uuid}')");
            return (new Message())->defaultMessage(1, 200, $result);

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function searchUserAccount($uuid, Request $request)
    {
        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $json = (new MassiveJsonConverter())->generate('SEARCH', $request);
            $result = DB::select("CALL SP_SEARCH_USER_ACCOUNT('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getCashBackList($uuid, Request $request)
    {
        Validator::make($request->all(),[
            'DATE' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $result = DB::select("CALL SP_GET_CASHBACK_LIST('{$request->DATE}', '{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getPaymentOrderList($uuid, Request $request)
    {
        Validator::make($request->all(),[
            'DATE_START' => 'required',
            'DATE_END' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $result = DB::select("CALL SP_GET_PAYMENT_ORDER_LIST('{$request->DATE_START}', '{$request->DATE_END}', '{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function CashbackReport(Request $request)
    {
        Validator::make($request->all(),[
            'USER_ACCOUNT_ID' => 'required',
            'ADM_UUID' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $request->ADM_UUID)->first();

        if(! $adm) {
            return (new Message())->defaultMessage(27, 404);
        }

        if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
            return (new Message())->defaultMessage(41, 403);
        }

        $result = DB::select("CALL SP_REL_CASHBACK_PERIOD_BLOCK({$request->USER_ACCOUNT_ID})");

        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function exportPaymentOrderList($uuid, Request $request)
    {
        Validator::make($request->all(),[
            'DATE_START' => 'required',
            'DATE_END' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $result = DB::select("CALL SP_GET_PAYMENT_ORDER_LIST('{$request->DATE_START}', '{$request->DATE_END}', '{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                date_default_timezone_set ( 'Asia/Tokyo');
                $today = date('Ymd_Hi');
                $name = "PAYMENT_ORDER_REPORT_{$today}";

                $dir = $_SERVER['DOCUMENT_ROOT'].'/storage/exports/';

                if (!file_exists($dir)){
                    File::makeDirectory($dir);
                }
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A'.'1', "DT_PAYMENT");
                $sheet->setCellValue('B'.'1', "USER_ACCOUNT_ID");
                $sheet->setCellValue('C'.'1', "NAME");
                $sheet->setCellValue('D'.'1', "DOCUMENT");
                $sheet->setCellValue('E'.'1', "NICKNAME");
                $sheet->setCellValue('F'.'1', "ORDER_ITEM_ID");
                $sheet->setCellValue('G'.'1', "PAYMENT_METHOD");
                $sheet->setCellValue('H'.'1', "GLOSS_PRICE");
                $sheet->setCellValue('I'.'1', "DISCOUNT");
                $sheet->setCellValue('J'.'1', "UPGRADE");
                $sheet->setCellValue('K'.'1', "BILLET_DIGITABLE_LINE");
                $sheet->setCellValue('L'.'1', "BILLET_NET_PRICE");
                $sheet->setCellValue('M'.'1', "PRODUCT_SCORE");
                $sheet->setCellValue('N'.'1', "LAUNCHED_SCORE");
                $sheet->setCellValue('O'.'1', "TOTAL_LAUNCHED_STORE");

                $count = count($result);
                for ($i = 0; $i < $count; $i++){
                    $sheet->setCellValue('A'.($i+2), $result[$i]->DT_PAYMENT);
                    $sheet->setCellValue('B'.($i+2), $result[$i]->USER_ACCOUNT_ID);
                    $sheet->setCellValue('C'.($i+2), $result[$i]->NAME);
                    $sheet->setCellValue('D'.($i+2), $result[$i]->DOCUMENT);
                    $sheet->setCellValue('E'.($i+2), $result[$i]->NICKNAME);
                    $sheet->setCellValue('F'.($i+2), $result[$i]->ORDER_ITEM_ID);
                    $sheet->setCellValue('G'.($i+2), $result[$i]->PAYMENT_METHOD);
                    $sheet->setCellValue('H'.($i+2), $result[$i]->GLOSS_PRICE);
                    $sheet->setCellValue('I'.($i+2), $result[$i]->DISCOUNT);
                    $sheet->setCellValue('J'.($i+2), $result[$i]->UPGRADE);
                    $sheet->setCellValue('K'.($i+2), $result[$i]->BILLET_DIGITABLE_LINE);
                    $sheet->setCellValue('L'.($i+2), $result[$i]->BILLET_NET_PRICE);
                    $sheet->setCellValue('M'.($i+2), $result[$i]->PRODUCT_SCORE);
                    $sheet->setCellValue('N'.($i+2), $result[$i]->LAUNCHED_SCORE);
                    $sheet->setCellValue('O'.($i+2), $result[$i]->TOTAL_LAUNCHED_STORE);
                }

                $writer = new Xlsx($spreadsheet);

                $writer->save('storage/exports/'.$name.'.xlsx');

                return response()->file("storage/exports/{$name}.xlsx", [ 'Content-Disposition' => "inline; filename={$name}.xlsx"]);
            }else{
                return (new Message())->defaultMessage($id, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getTransferUserAccount($uuid, Request $request)
    {
        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }
            $result = DB::select("CALL SP_GET_TRANSFER_USER_ACCOUNT_LIST('{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function updateNickname($uuid, Request $request)
    {

        Validator::make($request->all(),[
            'P_USER_ID' => 'required',
            'P_USER_ACCOUNT_ID' => 'required',
            'P_NICKNAME_OLD' => 'required',
            'P_NICKNAME_NEW' => 'required',
            'P_NOTE' => 'required',
            'P_SYSTEM_ID' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_UPDATE_NICKNAME(
                                                                {$request->P_USER_ID},
                                                                 {$request->P_USER_ACCOUNT_ID},
                                                                  '{$request->P_NICKNAME_OLD}',
                                                                   '{$request->P_NICKNAME_NEW}',
                                                                    '{$request->P_NOTE}',
                                                                     '{$uuid}',
                                                                      {$request->P_SYSTEM_ID})");
            if($result[0]->CODE_LIST_ID == 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($result[0]->CODE_LIST_ID, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getUplineNetwork($uuid, Request $request)
    {
        Validator::make($request->all(),[
            'NICKNAME' => 'required'
        ])->validate();

        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::where('NICKNAME', $request->NICKNAME)->first();
            if($userAccount){
                $result = DB::select("CALL SP_GET_UPLINE_LIST({$userAccount->ID}, '{$uuid}', @P_CODE_LIST_ID)");
                $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
                if($id == 1){
                    return (new Message())->defaultMessage(1, 200, $result);
                }else{
                    return (new Message())->defaultMessage($id, 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getTransferLog($uuid, Request $request)
    {
        $adm = $this->adm->where('UUID', $uuid)->first();

        if($adm) {
            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_TRANSFER_PAYMENT_LOG_LIST('{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function pendentWithdrawalRequest($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_WITHDRAWALL('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(40, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function approveWithdrawalRequest($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'WITHDRAWAL_REQUEST_ID' => 'required',
            'STATUS' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $withdrawal = WithDrawlRequest::find($request->WITHDRAWAL_REQUEST_ID);
            if($withdrawal){
                if($withdrawal->WITHDRAWAL_STATUS_ID != 1){
                    return response()->json(['ERROR' => 'THIS WITHDRAWAL REQUEST CAN NOT BE CHANGED'], 400);
                }
                $status = '';
                $description = '';
                if($request->STATUS == 'paid'){
                    $status = 'APPROVED MANUALLY';
                    $description = 'MANUALLY APPROVED BY THE ADMINISTRATOR PLATFORM';
                }else{
                    $status = 'APPROVED REFUSED';
                    $description = 'MANUALLY REFUSED BY THE ADMINISTRATOR PLATFORM';
                }

                $user = User::find($withdrawal->USER_ID);
                $today = date('Y-m-d');
                $amount = $withdrawal->NET_AMOUNT*5;

                $result = DB::select("CALL SP_CONFIRM_WITHDRAWAL(
                                                                        {$withdrawal->ID},
                                                                        1,
                                                                        '{$user->DOCUMENT}',
                                                                        '{$today}',
                                                                        '{$amount}',
                                                                        '{$request->STATUS}',
                                                                        '{$status}',
                                                                        '{$description}',
                                                                        '{$uuid}')");

                return (new Message())->defaultMessage(1, 200);
            }else{
                return response()->json(['ERROR' => 'WITHDRAWAL REQUEST NOT FOUND'], 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function withdrawalReport($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $json = ((new MassiveJsonConverter())->generate("SEARCH", $request));
            $result = DB::select("CALL SP_SEARCH_WITHDRAWAL_LOG('{$json}', '{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage(40, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function newUserEmail($uuid, Request $request)
    {

        Validator::make($request->all(), [
            'P_USER_ID' => 'required',
            'P_OLD_EMAIL' => 'required',
            'P_NEW_EMAIL' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $user = User::find($request->P_USER_ID);
            if($user){
                $result = DB::select("CALL SP_NEW_EMAIL_UPDATE(
                                                                '{$uuid}',
                                                                {$request->P_USER_ID},
                                                                '{$request->P_OLD_EMAIL}',
                                                                '{$request->P_NEW_EMAIL}'
                                                                )");
                if($result[0]->CODE == 1){
                    $token = (new Crypto())->newEmailEncrypt($result[0]->EMAIL_UPDATE_ID, $request->P_USER_ID, $request->P_OLD_EMAIL, $request->P_NEW_EMAIL);
                    $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->newUserEmail($request->P_OLD_EMAIL, $request->P_NEW_EMAIL, $token);

                    $email = explode('@', $request->P_NEW_EMAIL);

                    if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                        $mail = Mail::to($request->P_NEW_EMAIL)->send(new NewUserEmailMail($html));
                        $mail = true;
                    }else{
                        $mg = new MailGunFactory();
                        $mail = $mg->send($request->P_NEW_EMAIL, 'Troca de email!', $html);
                    }

                    return (new Message())->defaultMessage(1, 200);

                }else{
                    return (new Message())->defaultMessage($result[0]->CODE, 400);
                }
            }else{
                return (new Message())->defaultMessage(18, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function verifyNewUserEmail($token)
    {
        $token = (new Crypto())->newEmailDecrypt($token);

        if($token === false){
            //RETURN TO ERROR VIEW
            return redirect(env('FRONT_URL')."login#changeemailfail");
        }

        if (!property_exists($token, 'tid') || !property_exists($token, 'uid') || !property_exists($token, 'oem') || !property_exists($token, 'nem')) {
            //RETURN TO ERROR VIEW
            return redirect(env('FRONT_URL')."login#changeemailfail");
        }

        $user = User::where('EMAIL', $token->oem)->first();
        if($user){
            $password = $user->ID.$token->nem;
        }

        $result = DB::select("CALL SP_CONFIRM_EMAIL_UPDATE(
                                                                {$token->tid},
                                                                {$token->uid},
                                                                '{$token->oem}',
                                                                '{$token->nem}'
                                                                )");
        if($result[0]->CODE == 1){
            Http::post(env('VG_SCHOOL')."/api/change-user-data-for-api", [
                'old_email' => $token->oem,
                'new_email' => $token->nem,
                'password' => $password,
            ]);

            return redirect(env('FRONT_URL')."login#changeemailsuccess");
        }else{
            return redirect(env('FRONT_URL')."login#changeemailfail");
        }
    }

    public function findNicknameByUser($uuid, Request $request)
    {

        Validator::make($request->all(), [
            'VALUE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $id = false;

            $user = User::where('EMAIL', $request->VALUE)->where('EXTERNAL_CLIENT', 0)->first();
            if($user){
                $id = $user;
            }
            $user = User::where('DOCUMENT', $request->VALUE)->where('EXTERNAL_CLIENT', 0)->first();
            if($user){
                $id = $user;
            }
            $user = User::where('NAME', 'LIKE', $request->VALUE)->where('EXTERNAL_CLIENT', 0)->first();
            if($user){
                $id = $user;
            }

            if($id != false){
                $userAccounts = UserAccount::where('USER_ID', $id->ID)->get(['ID', 'NICKNAME', 'SPONSORED_ACCOUNT']);
                $data = [
                    'NAME' => $id->NAME,
                    'SOCIAL_REASON' => $id->SOCIAL_REASON,
                    'EMAIL' => $id->EMAIL,
                    'DOCUMENT' => $id->DOCUMENT,
                    'NICKNAMES' => [
                        $userAccounts
                    ]
                ];
                return (new Message())->defaultMessage(1, 200, $data);
            }else{
                return (new Message())->defaultMessage(18, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function findExternalUser($uuid, Request $request)
    {

        Validator::make($request->all(), [
            'VALUE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm) {

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $user = User::where('EMAIL', $request->VALUE)->where('EXTERNAL_CLIENT', 1)->first();
            if(!$user) $user = User::where('DOCUMENT', $request->VALUE)->where('EXTERNAL_CLIENT', 1)->first();
            if(!$user) $user = User::where('NAME', 'LIKE', '%' . $request->VALUE . '%')->where('EXTERNAL_CLIENT', 1)->get();

            return (new Message())->defaultMessage(1, 200, $user->makeHidden(['PASSWORD', 'FINANCIAL_PASSWORD', 'ACCEPTED_TERM']));
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }


    public function createNewAdm($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'EMAIL' => 'required',
            'PASSWORD' => 'required',
            'ACCESS_LEVEL_ID' => 'required',
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){

            if ((new JwtValidation())->validateByAdm($adm->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            if($adm->ACCESS_LEVEL_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'USER NOT ALLOWED']], 403);
            }

            $existEmail = $adm->verifyEmail($request->EMAIL);
            if($existEmail){
                return (new Message())->defaultMessage(24, 400);
            }

            $access_level = AccessLevel::find($request->ACCESS_LEVEL_ID);
            if($access_level){
                DB::select("INSERT INTO ADM (NAME, EMAIL, PASSWORD, ACCESS_LEVEL_ID, ACTIVE) VALUES ('{$request->NAME}', '{$request->EMAIL}', sha2('{$request->PASSWORD}', 256), $request->ACCESS_LEVEL_ID, 1)");
                $success = Adm::where('EMAIL', $request->EMAIL)->first();
                if($success){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return (new Message())->defaultMessage(20, 400);
                }
            }else{
                return (new Message())->defaultMessage(17, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getAdmUserList($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            if($request->has('status')){
                if($request->status === 'active') {
                    $permissions = $this->adm->where('ACTIVE', 1)->get();
                }elseif($request->status === 'inactive'){
                    $permissions = $this->adm->where('ACTIVE', 0)->get();
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'STATUS FOR ADM NOT FOUND, ONLY ALLOWED ACTIVE OR INACTIVE']], 400);
                }
            }else{
                $permissions = $this->adm->all();
            }
            return (new Message())->defaultMessage(1, 200, $permissions->makeHidden('PASSWORD'));
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function activeOrInactive($uuid, $id, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            if($adm->ACCESS_LEVEL_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'USER NOT ALLOWED']], 403);
            }

            $targetId = Adm::find($id);
            if($targetId){
                if($targetId->ACTIVE === 1){
                    DB::select("UPDATE ADM SET ACTIVE = 0 WHERE ID = {$targetId->ID}");
                }else{
                    DB::select("UPDATE ADM SET ACTIVE = 1 WHERE ID = {$targetId->ID}");
                }
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(27, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function changeAdmLevel($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ADM_ID' => 'required',
            'ACCESS_LEVEL_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            if($adm->ACCESS_LEVEL_ID != 1){
                return response()->json(['ERROR' => ['MESSAGE' => 'USER NOT ALLOWED']], 403);
            }

            $targetId = Adm::find($request->ADM_ID);
            if($targetId){
                DB::select("UPDATE ADM SET ACCESS_LEVEL_ID = {$request->ACCESS_LEVEL_ID} WHERE ID = {$targetId->ID}");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(27, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getUserLogList($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_LOG_USER_LIST({$request->P_USER_ID})");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function searchTransfer($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            MassiveJsonConverter::removeInvalidFields([
                "NICKNAME", "DOCUMENT",
                "NAME", "SOCIAL_REASON",
                "DT_REGISTER_START", "DT_REGISTER_END",
                "DT_TRANSFER_START", "DT_TRANSFER_END",
                "DIGITAL_PLATFORM_ID", "HASH",
                "ID_TRANSFER", "TRANSFER_TO",
                "AMOUNT", "USER_ACCOUNT_ID",
                "ORDER_ITEM_ID", "VS_ORDER_ID"
            ], $request);
            $json = (new MassiveJsonConverter())->generate('SEARCH', $request);
            $result = DB::select("CALL SP_SEARCH_TRANSFER_PAYMENT('{$json}', '{$uuid}', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function approveSponsoredAccount($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'P_ORDER_ITEM_ID' => 'required',
            'P_USER_ACCOUNT_ID' => 'required',
            'P_EXPIRATION_DATE' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $order = OrderItem::find($request->P_ORDER_ITEM_ID);
            if($order){
                $result = DB::select("CALL SP_APPROVE_PAYMENT_SPONSORED_ACCOUNT({$request->P_ORDER_ITEM_ID}, {$request->P_USER_ACCOUNT_ID}, '{$request->P_EXPIRATION_DATE}', '{$uuid}')");
                if($result[0]->CODE == 1){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return (new Message())->defaultMessage($result[0]->CODE, 400);
                }
            }else{
                return (new Message())->defaultMessage(28, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function insertVsUplineScore(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ACCOUNT_ID' => 'required',
            'VS_ORDER_ID' => 'required',
            'SCORE' => 'required',
            'ADM_UUID' => 'required',
            'CHIP_NUMBER' => 'required'
        ])->validate();

        try {
            $result = DB::select("CALL SP_INSERT_VS_UPLINE_SCORE_ADM(
                {$request->USER_ACCOUNT_ID},
                {$request->VS_ORDER_ID},
                '{$request->SCORE}',
                '{$request->ADM_UUID}',
                '{$request->CHIP_NUMBER}'
            )");

            if ($result[0]->CODE == 1){
                DB::select("UPDATE VS_ICCID_CHIP SET POINT = 1 WHERE NUMBER = '{$request->CHIP_NUMBER}'");
                return (new Message())->defaultMessage(1, 200);
            }

            return (new Message())->defaultMessage($result[0]->CODE, 400);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ["MESSAGE" => "UNEXPECTED ERROR, TRY AGAIN"]], 400);
        }
    }

    public function insertUplineScoreChip(Request $request)
    {
        Validator::make($request->all(), [
            'P_NICKNAME_SPONSOR' => 'required',
            'P_ICCID_CHIP_NUMBER' => 'required',
            'P_DT_PAYMENT' => 'required',
            'P_NUMBER_PHONE' => 'required',
            'P_SCORE' => 'required',
            'P_ADM_UUID' => 'required',
        ])->validate();

        try {
            $result = DB::select("CALL SP_INSERT_UPLINE_SCORE_CHIP(
                '{$request->P_NICKNAME_SPONSOR}',
                {$request->P_ICCID_CHIP_NUMBER},
                '{$request->P_DT_PAYMENT}',
                {$request->P_NUMBER_PHONE},
                {$request->P_SCORE},
                '{$request->P_ADM_UUID}'
            )");

            if ($result[0]->CODE != 1) {
                return (new Message())->defaultMessage($result[0]->CODE, 400);
            }

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e) {
            return response()->json(
                [
                    'ERROR' => [
                        "MESSAGE" => "UNEXPECTED ERROR, TRY AGAIN"
                    ],
                    'MESSAGE' => $e->getMessage(),
                    'FILE' => $e->getFile(),
                    'LINE' => $e->getLine()
                ], 400);
        }
    }
}


