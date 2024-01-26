<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserAccountRequest;
use App\Mail\CompraBoletoMail;
use App\Mail\TransferNicknameMail;
use App\Mail\UserAccountMail;
use App\Models\Adm;
use App\Models\CashBackWeek;
use App\Models\User;
use App\Models\UserAccount;
use App\Utils\Crypto;
use App\Utils\FileHandler;
use App\Utils\HtmlWriter;
use App\Utils\JwtValidation;
use App\Utils\MailGunFactory;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserAccountController extends Controller
{
    private $userAccount;

    public function __construct(UserAccount $userAccount)
    {
        $this->userAccount = $userAccount;
    }

    public function index()
    {
        $users = $this->userAccount->all();
        return (new Message())->defaultMessage(1, 200, $users->makeHidden(['UUID', 'USER_ID', 'SEQ', 'PARENT_ID'
        , 'SPONSOR_ID', 'SIDE', 'PREFERENTIAL_SIDE', 'ACTIVE', 'DT_REGISTER', 'PREVIOUS_USER_ID', 'QUALIFIER_RIGHT_ID',
            'QUALIFIER_LEFT_ID', 'ADM_ID','DT_LAST_UPDATE_ADM', 'NOTE', 'DT_ACTIVE_TUDDO_PAY', 'WITHDRAWAL_BY_TUDDO_PAY', 'GENRE_ID']));
    }

    public function show($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if(!$user){
            return (new Message())->defaultMessage(13, 404);
        }else{

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            return response()->json($user);
        }
    }

    public function clearCareerPath(Request $request)
    {
        Validator::make($request->all(), [
            'adm_uuid' => 'required',
            'nickname' => 'required'
        ])->validate();

        $result = DB::select("CALL SP_CLEAR_CAREER_PATH('{$request->nickname}','{$request->adm_uuid}')");
        if ($result[0]->CODE == 1){
            return (new Message())->defaultMessage(1, 200);
        }

        return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_CLEAR_CAREER_PATH');
    }

    public function newUserAccountForExternalClient(Request $request)
    {
        Validator::make($request->all(), [
            'user_id' => 'required',
            'nickname' => 'required',
        ])->validate();

        $userAccount = $this->userAccount->where('NICKNAME', $request->nickname)->first();
        if ($userAccount){
            return (new Message())->defaultMessage(8, 400);
        }

        $data = new stdClass();

        if ($request->has('sponsor_id')){
            $data->sponsorId = $request->sponsor_id;
        }else{
            $data->sponsorId = 'NULL';
        }

        $data->userId = $request->user_id;
        $data->nickname = $request->nickname;

        $result = DB::select("CALL SP_NEW_USER_ACCOUNT_FOR_EXTERNAL_CLIENT({$data->userId}, '{$data->nickname}', {$data->sponsorId})");
        if ($result[0]->CODE == 1){
            return (new Message())->defaultMessage(1, 200);
        }
        return (new Message())->defaultMessage(17, 400);
    }

    public function getNicknamesForCareerPath($id)
    {
        $user = User::find($id);
        if(!$user) return (new Message())->defaultMessage(17, 404);
        $data = DB::select("SELECT UA.ID AS USER_ACCOUNT_ID,
                             UA.NICKNAME
                          FROM USER US
                          JOIN USER_ACCOUNT UA
                             ON US.ID = UA.USER_ID
                            AND UA.ACTIVE
                        WHERE NOT EXISTS ( SELECT 1
                                                    FROM ORDER_TRACKING OT
                                                  WHERE UA.ID = OT.USER_ACCOUNT_ID
                                                 AND OT.ID = FN_LAST_ORDER_TRACKING(UA.ID)
                                                 AND OT.SPONSORED_ACCOUNT)
                          AND US.ID = {$id}");
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function store(UserAccountRequest $request)
    {
        //VERIFICA SE O USUARIO E A SENHA EXISTEM NO DB

        $result = DB::select("CALL SP_AUTHENTICATE_LOGIN('{$request->P_EMAIL}', '{$request->P_PASSWORD}', 1, NULL, @ID)");
        if($result[0]->CODE == 1){
            $id = DB::select("SELECT @ID as id")[0]->id;
        }else{
            return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_AUTHENTICATE_LOGIN');
        }

        //VERIFICA SE O SPONSOR EXISTE
        $existing_sponsor_id = DB::select("SELECT FN_EXISTING_SPONSOR_ID({$request->P_SPONSOR_ID}) AS id")[0]->id;
        if(!$existing_sponsor_id){
            return (new Message())->defaultMessage(2, 404);
        }

        //VERIFICA A DISPONIBILIDADE DO NICKNAME
        $existing_nickname = DB::select("SELECT FN_EXISTING_NICKNAME('{$request->P_NICKNAME}', '{$request->P_SPONSOR_ID}') as nickname")[0]->nickname;
        if($existing_nickname){
            return (new Message())->defaultMessage(8, 400);
        }

        DB::select("CALL SP_NEW_USER_ACCOUNT (1,?,?,?,?,?,@ID)",
            array(
                $id,
                $request->P_SPONSOR_ID,
                $request->P_SIDE,
                $request->P_NICKNAME,
                NULL,
                "@ID"
            ));

        $result = DB::select("SELECT @ID as result")[0]->result;
        if($result == 1){
            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function otherAccountRequest(Request $request)
    {
        Validator::make($request->all(), [
            'P_EMAIL' => 'required',
            'P_PASSWORD' => 'required',
            'P_OWNER_EMAIL' => 'required',
            'P_SPONSOR_ID' => 'required',
            'P_NICKNAME' => 'required',
            'P_SIDE' => 'required'
        ])->validate();

        $result = DB::select("CALL SP_AUTHENTICATE_LOGIN('{$request->P_EMAIL}', '{$request->P_PASSWORD}', 1, NULL, @ID)");
        if($result[0]->CODE != 1){
            return (new Message())->defaultMessage(10, 400);
        }

        $existing_sponsor_id = DB::select("SELECT FN_EXISTING_SPONSOR_ID({$request->P_SPONSOR_ID}) AS id")[0]->id;
        if(!$existing_sponsor_id){
            return (new Message())->defaultMessage(2, 404);
        }

        $sponsor = $this->userAccount->find($request->P_SPONSOR_ID);

        $existing_nickname = DB::select("SELECT FN_EXISTING_NICKNAME('{$request->P_NICKNAME}', '{$request->P_SPONSOR_ID}') as nickname")[0]->nickname;
        if($existing_nickname){
            return (new Message())->defaultMessage(8, 400);
        }

        if($request->P_SIDE == 1 || $request->P_SIDE == 2 || $request->P_SIDE == 3){
            $user = User::where('EMAIL', $request->P_OWNER_EMAIL)->first();
            if($user){
                $token = (new Crypto())->userAccountRequestEncrypt($user->ID, $sponsor->ID, $request->P_SIDE, $request->P_NICKNAME);
                $html = (new HtmlWriter($user->NAME ? $user->NAME : $user->SOCIAL_REASON))->userAccountRequest($sponsor->NICKNAME, $request->P_NICKNAME, $token);
                $mg = new MailGunFactory();

                $email = explode('@', $user->EMAIL);

                if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                    $mail = Mail::to($user->EMAIL)->send(new UserAccountMail($html));
                    $mail = true;
                }else{
                    $mail = $mg->send($user->EMAIL, 'Novo Nickname Solicitado!', $html);
                }

                if($mail){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'THERE WAS AN ERROR SENDING EMAIL']], 400);
                }

            }else{
                return response()->json(['DESCRIPTION' => 'PENDING EMAIL CONFIRMATION', 'REF' => 'I0000'], 400);
            }
        }else{
            return response()->json(['ERROR' => ['MESSAGE' => 'INVALID SIDE']], 400);
        }
    }

    public function validateOtherAccountRequest($token)
    {
        $data = (new Crypto())->userAccountRequestDecrypt($token);

        if(is_null($data)){
            return redirect(env('FRONT_URL')."login#addnicknamefail");
        }
        $exp = new \DateTime($data->exp->date);
        $now = new \DateTime();
        $diff = date_diff($exp, $now);
        if($diff->days >= 1){
            return redirect(env('FRONT_URL')."login#addnicknamefail");
        }else{
            $existing_sponsor_id = DB::select("SELECT FN_EXISTING_SPONSOR_ID({$data->sid}) AS id")[0]->id;
            if(!$existing_sponsor_id){
                    return redirect(env('FRONT_URL')."login#addnicknamefail");
            }

            $existing_nickname = DB::select("SELECT FN_EXISTING_NICKNAME('{$data->nick}', '{$data->sid}') as nickname")[0]->nickname;
            if($existing_nickname){
                    return redirect(env('FRONT_URL')."login#addnicknamefail");
            }

            $user = User::find($data->uid);
            if(!$user){
                    return redirect(env('FRONT_URL')."login#addnicknamefail");
            }

            if($data->side == 1 || $data->side == 2 || $data->side == 3){
                if ($user->EXTERNAL_CLIENT){
                    $result = DB::select("CALL SP_NEW_USER_ACCOUNT_FOR_EXTERNAL_CLIENT({$data->uid}, '{$data->nick}', {$data->sid})");
                    if ($result[0]->CODE == 1){
                        $result = 1;
                    }else{
                        $result = $result[0]->CODE;
                    }
                }else{
                    DB::select("CALL SP_NEW_USER_ACCOUNT (1,?,?,?,?,?,@ID)",
                        array(
                            $data->uid,
                            $data->sid,
                            $data->side,
                            $data->nick,
                            NULL,
                            "@ID"
                        ));

                    $result = DB::select("SELECT @ID as result")[0]->result;
                }

                if($result == 1){
                    return redirect(env('FRONT_URL')."login#addnicknamesuccess");
                }else{
                    return redirect(env('FRONT_URL')."login#addnicknamefail");
                }
            }else{
                if ($user->EXTERNAL_CLIENT) {
                    $result = DB::select("CALL SP_NEW_USER_ACCOUNT_FOR_EXTERNAL_CLIENT({$data->uid}, '{$data->nick}', {$data->sid})");
                    if ($result[0]->CODE == 1) {
                        return redirect(env('FRONT_URL') . "login#addnicknamesuccess");
                    } else {
                        return redirect(env('FRONT_URL') . "login#addnicknamefail");
                    }
                }
                return redirect(env('FRONT_URL')."login#addnicknamefail");
            }
        }


    }

    public function update($id, Request $request)
    {
        $data = $request->all();

        if($this->userAccount->where('NICKNAME', $data['NICKNAME'])){
            return (new Message())->defaultMessage(8, 400);
        }

        foreach ($request->all() as $key => $value) {
            DB::select("UPDATE USER_ACCOUNT SET {$key} = '{$value}' WHERE id = {$id}");
        }

        return (new Message())->defaultMessage(22, 203);
    }

    public function getNetWorkList(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_REF_USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $user = $this->userAccount->find($request->P_USER_ACCOUNT_ID);
        $userREF = $this->userAccount->find($request->P_REF_USER_ACCOUNT_ID);

        if(!$user || !$userREF){
            return (new Message())->defaultMessage(13, 404);
        }else{
            $result = DB::select("CALL SP_GET_NETWORK_LIST ({$request->P_USER_ACCOUNT_ID}, {$request->P_REF_USER_ACCOUNT_ID})");
            return (new Message())->defaultMessage(1, 200, $result);
        }
    }

    public function getNetworkListByNickname(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ACCOUNT_ID' => 'required',
            'P_REF_NICKNAME' => 'required'
        ])->validate();

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
    }

    public function getSponsorsList($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_SPONSORS_LIST ({$id})");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function upPreferentialSide(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_SIDE' => 'required'
        ])->validate();

        $user = $this->userAccount->find($request->P_USER_ACCOUNT_ID);
        if($user){

            if((new JwtValidation())->validateByUserAccount($request->P_USER_ACCOUNT_ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            if($request->P_SIDE == 1 || $request->P_SIDE == 2 || $request->P_SIDE == 3){
                DB::select("CALL SP_UP_PREFERENTIAL_SIDE ({$request->P_USER_ACCOUNT_ID}, {$request->P_SIDE})");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return response()->json(['ERROR' => ['MESSAGE' => 'INVALID SIDE']], 400);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getOrderItemList($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_ORDER_ITEM_LIST ({$id})");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getEstatementList(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_DATE'=> 'required'
        ])->validate();

        $user = $this->userAccount->find($request->P_USER_ACCOUNT_ID);
        if($user){

            if((new JwtValidation())->validateByUserAccount($request->P_USER_ACCOUNT_ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_ACCOUNT_STATEMENT_LIST ({$request->P_USER_ACCOUNT_ID}, '{$request->P_DATE}')");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getCashBackWeekValidateList($id)
    {
        $user = $this->userAccount->find($id);
        if($user){
            $result = DB::select("CALL SP_GET_UA_CASHBACK_WEEK_VALIDATE_LIST({$id})");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getCashBackWeekList($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){
            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $result = DB::select("CALL SP_GET_UA_CASHBACK_WEEK_LIST({$id})");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function newCashBackWeek(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_CASHBACK_WEEK_ID'=> 'required'
        ])->validate();

        $user = $this->userAccount->find($request->P_USER_ACCOUNT_ID);
        if($user){
            $cash = CashBackWeek::find($request->P_CASHBACK_WEEK_ID);
            if($cash){
                $result = DB::select("CALL SP_NEW_UA_CASHBACK_WEEK({$request->P_USER_ACCOUNT_ID}, {$request->P_CASHBACK_WEEK_ID})");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(17, 404);
            }
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getBinaryChartList(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_DAYS'=> 'required'
        ])->validate();

        $user = $this->userAccount->find($request->P_USER_ACCOUNT_ID);

        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_BINARY_CHART_LIST({$request->P_USER_ACCOUNT_ID}, {$request->P_DAYS})");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }
    public function getLastNetworkLeg(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_SIDE'=> 'required'
        ])->validate();

        if($request->P_SIDE == 1 || $request->P_SIDE == 2 || $request->P_SIDE == 3){
            $user = $this->userAccount->find($request->P_USER_ACCOUNT_ID);
            if($user){

                if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                    return (new Message())->defaultMessage(41, 403);
                }

                $result = DB::select("SELECT FN_GET_LAST_NETWORK_LEG({$request->P_USER_ACCOUNT_ID}, {$request->P_SIDE}) as UserAccountId");
                return (new Message())->defaultMessage(1, 200, $result[0]);
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function blockedCashBackList($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $result = DB::select("CALL SP_BLOCKED_CASHBACK_LIST({$id})");
            $start = (DB::select("SELECT FN_DT_START_BLOCKING_PERIOD({$id}) as result"))[0]->result;
            $end = (DB::select("SELECT FN_DT_END_BLOCKING_PERIOD({$id}) as result"))[0]->result;
            $data = ["List" => $result,
                "Start" => $start,
                "End" => $end];
            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getUserAccountInformation($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_USER_ACCOUNT_INFORMATION({$id})");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getUserAccountInformationAll($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $uaInformation = DB::select("CALL SP_GET_USER_ACCOUNT_INFORMATION({$id})");
            $userAccounts = DB::select("CALL SP_GET_USER_ACCOUNT('{$user->USER_ID}')");
            $cashBackWeek = DB::select("CALL SP_GET_UA_CASHBACK_WEEK_VALIDATE_LIST({$user->USER_ID})");
            $result = ['UserAccountInformation' => $uaInformation,
                        'UserAccounts' => $userAccounts,
                        'CashBackValidateWeek' => $cashBackWeek];
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    // rota para pegar o plano de carreira
    public function getUserAccountPlan($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $carrerPath = DB::select("CALL SP_GET_USER_ACCOUNT_CAREER_PATH_LIST({$id})");
            $result = ['CareerPath' => $carrerPath];
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getIdByNickname($nickname)
    {
        $user = $this->userAccount->where('NICKNAME', $nickname)->first();
        if($user){
            return (new Message())->defaultMessage(1, 200, $user->ID);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    /* confirma ajuste carteira */
    public function setUserAccountCarteira(Request $request)
    {

        Validator::make($request->all(),[
            'P_USER_ID' => 'required',
        ])->validate();

        //$user = $this->userAccount->find($id);

        //if($user){

        $result = DB::select("UPDATE USER SET ACCEPTED_TERM = 1 WHERE ID = '{$request->P_USER_ID}'");

        return (new Message())->defaultMessage(1, 200);

    }

    /* fim ajuste carteira */

    public function setUserAccountProfileImage($id, Request $request)
    {
        Validator::make($request->all(), [
            'PROFILE_IMAGE' => 'required'
        ])->validate();

        $user = $this->userAccount->find($id);
        if ($user) {

            if ((new JwtValidation())->validateByUserAccount($user->ID, $request) == false) {
                return (new Message())->defaultMessage(41, 403);
            }

            $image = '';
            $way = 'userAccount-'.$user->ID;
            if (Storage::disk('public')->exists($way . '.pdf')) {
                $image = $way . '.pdf';
            } elseif (Storage::disk('public')->exists($way . '.jpg')) {
                $image = $way . '.jpg';
            } elseif (Storage::disk('public')->exists($way . '.jpeg')) {
                $image = $way . '.jpeg';
            } elseif (Storage::disk('public')->exists($way . '.png')) {
                $image = $way . '.png';
            }
            if (Storage::disk('public')->exists($image)) {
                return response()->json(['ERROR' => ["MESSAGE' => 'THIS USER JUST HAVE A PROFILE IMAGE"]], 400);
            } else {
                $file = (new FileHandler())->writeFile($request->PROFILE_IMAGE, 'userAccount', $user->ID);
                return (new Message())->defaultMessage(1, 200);
            }
        } else {
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function getUserAccountProfileImage($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $image = '';
            $way = 'userAccount-'.$user->ID;
            if (Storage::disk('public')->exists($way . '.pdf')) {
                $image = $way . '.pdf';
            } elseif (Storage::disk('public')->exists($way . '.jpg')) {
                $image = $way . '.jpg';
            } elseif (Storage::disk('public')->exists($way . '.jpeg')) {
                $image = $way . '.jpeg';
            } elseif (Storage::disk('public')->exists($way . '.png')) {
                $image = $way . '.png';
            }
            if(Storage::disk('public')->exists($image)){
                $file = (new FileHandler())->getFile($image);
                $profile = explode('.', $image);
                $profile_image = ['Name' => $profile[0],
                                    'Ext' => $profile[1],
                                    'Data' => $file];
                return (new Message())->defaultMessage(1, 200, $profile_image);
            }else{
                return response()->json(['ERROR' => ["MESSAGE' => THIS USER ACCOUNT DON'T HAVE A PROFILE IMAGE"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function removeUserAccountProfileImage($id, Request $request)
    {
        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $image = '';
            $way = 'userAccount-'.$user->ID;
            if (Storage::disk('public')->exists($way . '.pdf')) {
                $image = $way . '.pdf';
            } elseif (Storage::disk('public')->exists($way . '.jpg')) {
                $image = $way . '.jpg';
            } elseif (Storage::disk('public')->exists($way . '.jpeg')) {
                $image = $way . '.jpeg';
            } elseif (Storage::disk('public')->exists($way . '.png')) {
                $image = $way . '.png';
            }
            if(Storage::disk('public')->exists($image)){
                if((new FileHandler())->removeFile($image) == true){
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ["MESSAGE" => "ERROR OCCURRED WHEN REMOVING THE IMAGE"]], 400);
                }
            }else{
                return response()->json(['ERROR' => ["MESSAGE' => THIS USER DON'T HAVE A PROFILE IMAGE"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getNicknameById($id)
    {
        $user = $this->userAccount->find($id);
        if($user){
            return (new Message())->defaultMessage(1, 200, $user->NICKNAME);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function userAccountScoreList($id, Request $request)
    {

        Validator::make($request->all(),([
            'DATE' => 'required'
        ]))->validate();

        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $data = [];

            $leftData = DB::select("CALL SP_GET_USER_ACCOUNT_SCORE_LIST({$id}, '{$request->DATE}', 1)");

            if(empty($leftData)){
                $leftData = NULL;
            }
            $leftScore = DB::select("SELECT FN_GET_SCORE_LEG({$id},1) as result")[0]->result;
            $left = ["List" => $leftData, "Score" => $leftScore];

            $rightData = DB::select("CALL SP_GET_USER_ACCOUNT_SCORE_LIST({$id}, '{$request->DATE}', 2)");
            if(empty($rightData)){
                $rightData = NULL;
            }
            $rightScore = DB::select("SELECT FN_GET_SCORE_LEG({$id},2) as result")[0]->result;
            $right = ["List" => $rightData, "Score" => $rightScore];

            $data = ['Left' => $left, 'Right' => $right];

            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    //function para top five list
    public function topFiveList($id)
    {
        $user = $id;

        if($user){

            //if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
            //    return (new Message())->defaultMessage(41, 403);
            //}

            $listLeft = DB::select("CALL SP_TOP_SPONSERS_LIST({$user}, 1, 5)");


            //right
            $listRight = DB::select("CALL SP_TOP_SPONSERS_LIST({$user}, 2, 5)");


            $data = [
                "Left" => $listLeft,
                "Right" => $listRight
            ];

            return ($data);

        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function vsUserAccountScoreList($id, Request $request)
    {
        Validator::make($request->all(),([
            'DATE' => 'required'
        ]))->validate();

        $user = $this->userAccount->find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $data = [];

            $leftData = DB::select("CALL SP_GET_VS_USER_ACCOUNT_SCORE_LIST({$id}, '{$request->DATE}', 1)");

            if(empty($leftData)){
                $leftData = NULL;
            }
            $leftScore = DB::select("SELECT FN_GET_VS_SCORE_LEG({$id},1) as result")[0]->result;
            $left = ["List" => $leftData, "Score" => $leftScore];

            $rightData = DB::select("CALL SP_GET_VS_USER_ACCOUNT_SCORE_LIST({$id}, '{$request->DATE}', 2)");
            if(empty($rightData)){
                $rightData = NULL;
            }
            $rightScore = DB::select("SELECT FN_GET_VS_SCORE_LEG({$id},2) as result")[0]->result;
            $right = ["List" => $rightData, "Score" => $rightScore];

            $data = ['Left' => $left, 'Right' => $right];

            return (new Message())->defaultMessage(1, 200, $data);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getUserDataByNickName($uuid, Request $request)
    {

        Validator::make($request->all(), [
            'nickname' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){
            $user = $this->userAccount->where('NICKNAME', $request->nickname)->first();
            if($user){
                $owner = User::find($user->USER_ID);
                if($owner){
                    return (new Message())->defaultMessage(1, 200, $owner->makeHidden([
                        'PASSWORD', 'FINANCIAL_PASSWORD'
                    ]));
                }else{
                    return (new Message())->defaultMessage(18, 404);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function transferUserAccount($uuid, Request $request)
    {
        Validator::make($request->all(), [
            "P_USER_ACCOUNT_ID" => 'required',
            "P_USER_ID" => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();

        if($adm){
            $account = $this->userAccount->find($request->P_USER_ACCOUNT_ID);
            if($account){
                //$isCarrerPath = DB::select("SELECT FN_IS_CAREER_PATH({$request->P_USER_ACCOUNT_ID}) AS IS_CAREER_PATH");
                //if ($isCarrerPath[0]->IS_CAREER_PATH != 0) return response()->json(['ERROR' => ['DATA' => 'THIS NICKNAME IS A CAREER PATH']], 400);
                $user = User::find($request->P_USER_ID);
                if($user){
                    $result = DB::select("CALL SP_TRANSFER_USER_ACCOUNT('{$uuid}', {$request->P_USER_ACCOUNT_ID}, {$request->P_USER_ID}, @P_CODE_LIST_ID)");
                    $id = DB::select("SELECT @P_CODE_LIST_ID as code");
                    if($id[0]->code == 1){

                        $today = date('d/m/Y');
                        $owner = User::find($account->USER_ID);

                        $html = (new HtmlWriter($owner->NAME ? $owner->NAME : $owner->SOCIAL_REASON))->transferNickname($account->NICKNAME, $user->NAME, $today);
                        $mg = new MailGunFactory();

                        $email = explode('@', $owner->EMAIL);
                        if($email[1] === 'hotmail.com' || $email[1] === 'outlook.com' || $email[1] === 'live.com'){
                            $mail = Mail::to($owner->EMAIL)->send(new TransferNicknameMail($html));
                            $mail = true;
                        }else{
                            $mail = $mg->send($owner->EMAIL, 'Transferência de Nickname Realizada', $html);
                        }

                        if ($user->EXTERNAL_CLIENT == 1){
                            DB::select("UPDATE USER SET EXTERNAL_CLIENT = 0 WHERE ID = {$request->P_USER_ID}");
                        }

                        return (new Message())->defaultMessage(1, 200);
                    }else{
                        return (new Message())->defaultMessage($id[0]->code, 400);
                    }
                }else{
                    return (new Message())->defaultMessage(18, 404);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
