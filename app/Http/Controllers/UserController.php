<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Adm;
use App\Models\Product;
use App\Models\RegistrationRequest;
use App\Models\SendWhatsapp;
use App\Models\User;
use App\Models\UserAccount;
use App\Utils\FileHandler;
use App\Utils\JwtValidation;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use App\Utils\StoredProcedures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PDO;
use PDOStatement;



class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $data = $this->user->all();

        return (new Message())->defaultMessage(1, 200, $data->makeHidden(['PASSWORD', 'EMAIL', 'FINANCIAL_PASSWORD', 'ACCEPTED_TERM',
        'TYPE_PERSON_ID', 'TYPE_DOCUMENT_ID', 'DOCUMENT', 'VERIFIED_DOCUMENT', 'DT_VERIFIED_DOCUMENT', 'DT_BIRTHDAY', "REPRESENTATIVE",
            "SOCIAL_REASON", "FANTASY_NAME", "COUNTRY_ID", "ZIP_CODE", "ADDRESS", "NUMBER", "COMPLEMENT", "NEIGHBORHOOD", "CITY", "STATE", "DDI",
            "PHONE", "DT_REGISTER", "FIRST_INDICATION", "ACTIVE", "ACCEPTED_TERM", "BLOCKED", "ACTIVE_2FA", "TOKEN", "PREFERENTIAL_USER_ACCOUNT_ID",
            "ADM_ID", "SEQ_AUTOINCREMENT", "DT_LAST_UPDATE_ADM", "NOTE"]));
    }

    public function show($id, Request $request)
    {
        $data = $this->user->find($id);
        if(!$data){
            return (new Message())->defaultMessage(18, 400);
        }else{

            if((new JwtValidation())->validateByUser($data->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $resp = $data->makeHidden(['PASSWORD', 'FINANCIAL_PASSWORD', 'ACCEPTED_TERM']);
            $image = '';
            $way = 'user-'.$data->ID;
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
                $profilePicture = env('APP_URL').'/storage/'.$image;
            }else{
                $profilePicture = null;
            }

            $resp['PROFILE_PICTURE'] = $profilePicture;

            return (new Message())->defaultMessage(1, 200, $resp);
        }
    }

    public function store(UserRequest $request)
    {
        $existing_registration_request = DB::select("SELECT FN_EXISTING_REGISTRATION_REQUEST('{$request->P_REGISTRATION_REQUEST_ID}') as request")[0]->request;
        if($existing_registration_request != 1){
            return (new Message())->defaultMessage(7, 404);
        }
        $result = (new StoredProcedures())->newUser($request->P_SYSTEM_ID, $request->P_REGISTRATION_REQUEST_ID);
        if($result[0]->CODE == 1){
            return response()->json(['SUCCESS' => ['MESSAGE' => 'USER SUCCESSFULLY REGISTERED']], 200);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $user = $this->user->find($id);

        if(!$user){
            return (new Message())->defaultMessage(18, 404);
        }

        if((new JwtValidation())->validateByUser($user->ID, $request) == false){
            return (new Message())->defaultMessage(41, 403);
        }

        if($request->has('EMAIL') || $request->has('DOCUMENT') || $request->has('TYPE_DOCUMENT_ID')){
            return response()->json(['ERROR' => ['MESSAGE' => 'EMAIL AND DOCUMENT CANNOT BE CHANGED']], 400);
        }

        if($request->has('PASSWORD') || $request->has('FINANCIAL_PASSWORD')){
            return response()->json(['ERROR' => ['MESSAGE' => 'PASSWORD CANNOT BE CHANGED']], 403);
        }

        if($user->TYPE_DOCUMENT_ID == 1 || $user->TYPE_DOCUMENT_ID == 3){
            if($request->has('REPRESENTATIVE') || $request->has('SOCIAL_REASON') || $request->has('FANTASY_NAME')){
                return response()->json(['ERROR' => ['MESSAGE' => 'THIS USER IS A INDIVIDUAL']], 400);
            }

            if($request->has('NAME') && $user->NAME != null){
                return response()->json(['ERROR' => ['MESSAGE' => 'NAME FIELD CANNOT BE CHANGED']], 400);
            }
            if(!$request->has('NAME') && $user->NAME == null){
                return response()->json(['ERROR' => ['MESSAGE' => 'NAME FIELD IS REQUIRED']], 400);
            }
        }else{
            if($request->has('NAME')){
                return response()->json(['ERROR' => ['MESSAGE' => 'THIS USER IS A BUSINESS ACCOUNT']], 400);
            }

            if($request->has('SOCIAL_REASON') && $user->SOCIAL_REASON != null){
                return response()->json(['ERROR' => ['MESSAGE' => 'SOCIAL_REASON FIELD CANNOT BE CHANGED']], 400);
            }
            if(!$request->has('SOCIAL_REASON') && $user->SOCIAL_REASON == null){
                return response()->json(['ERROR' => ['MESSAGE' => 'SOCIAL_REASON FIELD IS REQUIRED']], 400);
            }
            if($request->has('REPRESENTATIVE') && $user->REPRESENTATIVE != null){
                return response()->json(['ERROR' => ['MESSAGE' => 'REPRESENTATIVE FIELD CANNOT BE CHANGED']], 400);
            }
            if(!$request->has('REPRESENTATIVE') && $user->REPRESENTATIVE == null){
                return response()->json(['ERROR' => ['MESSAGE' => 'REPRESENTATIVE FIELD IS REQUIRED']], 400);
            }
            if($request->has('FANTASY_NAME') && $user->FANTASY_NAME != null){
                return response()->json(['ERROR' => ['MESSAGE' => 'FANTASY_NAME FIELD CANNOT BE CHANGED']], 400);
            }
            if(!$request->has('FANTASY_NAME') && $user->FANTASY_NAME == null){
                return response()->json(['ERROR' => ['MESSAGE' => 'FANTASY_NAME FIELD IS REQUIRED']], 400);
            }
        }

        if(!$request->has('DT_BIRTHDAY') && $user->DT_BIRTHDAY == null){
            return response()->json(['ERROR' => ['MESSAGE' => 'DT_BIRTHDAY FIELD IS REQUIRED']], 400);
        }

        if(!$request->has('COUNTRY_ID') && $user->COUNTRY_ID == null){
            return response()->json(['ERROR' => ['MESSAGE' => 'COUNTRY_ID FIELD IS REQUIRED']], 400);
        }

        if($request->has('EXTERNAL_CLIENT') ){
            return response()->json(['ERROR' => ['MESSAGE' => 'EXTERNAL_CLIENT CANNOT BE CHANGED']], 400);
        }

        if($request->has('COUNTRY_ID') && $user->COUNTRY_ID == null){
            if($request->COUNTRY_ID == 27){
                if(!$request->has('ZIP_CODE') || !$request->has('ADDRESS') || !$request->has('NUMBER') || !$request->has('CITY') || !$request->has('STATE')) {
                    return response()->json(['ERROR' => ['MESSAGE' => 'FOR BRAZILIAN USERS, IT IS MANDATORY TO INFORM THE ZIP_CODE, ADDRESS, NUMBER, CITY, STATE AND NEIGHBORHOOD']], 400);
                }
            }else{
                if(!$request->has('CITY')) {
                    return response()->json(['ERROR' => ['MESSAGE' => 'CITY FIELD IS REQUIRED']], 400);
                }
            }
        }

        if($user->PHONE == null){
            if(!$request->has('PHONE') || !$request->has('DDI')){
                return response()->json(['ERROR' => ['MESSAGE' => 'PHONE AND DDI FIELD IS REQUIRED']], 400);
            }
        }

        try {
            if ($user->PHONE == null && ($request->has('PHONE') && $request->has('DDI'))){
                $data = [
                    'WHATSAPP' => $request->DDI . $request->PHONE,
                    'NAME' => $user->NAME,
                    'USER_ID' => $user->ID,
                ];

                if ($user->EXTERNAL_CLIENT != 1){
                    SendWhatsapp::sendMessage($data, SendWhatsapp::NEW_USER);
                }
            }

            foreach ($request->all() as $key => $value) {
                if($value != ""){
                    $value = strtoupper($value);
                    DB::select("UPDATE USER SET {$key} = UPPER('{$value}') WHERE id = {$id}");
                }
            }

            return (new Message())->defaultMessage(22, 203);
        }catch (\Exception $e){
            foreach ($request->all() as $key => $value) {
                if($value != ""){
                    $value = strtoupper($value);
                    DB::select("UPDATE USER SET {$key} = UPPER('{$value}') WHERE id = {$id}");
                }
            }

            return (new Message())->defaultMessage(22, 203);
        }
    }

    public function careerPath($id, Request $request)
    {
        Validator::make($request->all(), [
            'CARREAR_PATH_USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $user = $this->user->find($id);
        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::find($request->CARREAR_PATH_USER_ACCOUNT_ID);
            if($userAccount){
                if($user->CAREER_PATH_USER_ACCOUNT_ID != null) return response()->json(['ERROR' => ['DATA' => 'YOU ALREADY SET A NICKNAME FOR YOUR CAREER PATH']], 400);
                if($userAccount->USER_ID != $user->ID) return response()->json(['ERROR' => ['DATA' => 'THIS NICKNAME DOES NOT BELONGS TO YOUR ACCOUNT']], 400);
                try {
                    DB::select("UPDATE USER SET CAREER_PATH_USER_ACCOUNT_ID = {$userAccount->ID}, DT_SET_CAREER_PATH_USER_ACCOUNT = NOW() WHERE ID = {$user->ID}");
                    return (new Message())->defaultMessage(1, 200);
                }catch (\Exception $e){
                    return response()->json(['ERROR' => ['DATA' => 'DATA CAN NOT BE UPDATED, TRY AGAIN']], 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getUserAccount($id, Request $request)
    {
        $user = $this->user->find($id);
        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_USER_ACCOUNT('{$id}')");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getUserInformation($id, Request $request)
    {
        $user = $this->user->find($id);
        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_USER_INFORMATION('{$id}')");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getUserBank($id, Request $request)
    {
        $user = $this->user->find($id);
        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_USER_BANK('{$id}')");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function getUserWallet($id, Request $request)
    {
        $user = $this->user->find($id);

        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_USER_WALLET('{$id}')");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function setPreferentialUserAccount($id, Request $request)
    {
        Validator::make($request->all(), [
            'PREFERENTIAL_USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $user = $this->user->find($id);
        if($user){

            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $userAccount = UserAccount::find($request->PREFERENTIAL_USER_ACCOUNT_ID);
            if($userAccount){
                if($userAccount->USER_ID == $id){
                    DB::select("UPDATE USER SET PREFERENTIAL_USER_ACCOUNT_ID = '{$request->PREFERENTIAL_USER_ACCOUNT_ID}' WHERE ID = {$id}");
                    return (new Message())->defaultMessage(1, 200);
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'USER ACCOUNT DOES NOT BELONG TO THIS USER']], 400);
                }
            }else{
                return (new Message())->defaultMessage(13, 404);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function setUserProfileImage($id, Request $request)
    {
        Validator::make($request->all(), [
            'PROFILE_IMAGE' => 'required'
        ])->validate();

        $user = $this->user->find($id);

        $image = '';
        $way = 'user-'.$user->ID;
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
            $file = (new FileHandler())->writeFile($request->PROFILE_IMAGE, 'user', $user->ID);
            return (new Message())->defaultMessage(1, 200);
        }
    }


    public function getUserProfileImage($id, Request $request)
    {
        $user = $this->user->find($id);
        if($user){
            $image = '';
            $way = 'user-'.$user->ID;
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
                return response()->json(['ERROR' => ["MESSAGE' => THIS USER DON'T HAVE A PROFILE IMAGE"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function removeUserProfileImage($id, Request $request)
    {
        $user = $this->user->find($id);
        if($user){

            $image = '';
            $way = 'user-'.$user->ID;
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

    public function changePassword($id, Request $request)
    {
        Validator::make($request->all(), [
            'PASSWORD' => 'required',
            'TYPE' => 'required',
            'OLD_PASSWORD' => 'required'
        ])->validate();

        $user = UserAccount::find($id);
        if($user){

            if((new JwtValidation())->validateByUser($user->USER_ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            if($request->TYPE == 1 || $request->TYPE == 2){
                $field = '';

                if($request->TYPE == 1){
                    $user = User::find($user->USER_ID);
                    $verifyOldPassword = DB::select("CALL SP_AUTHENTICATE_LOGIN('{$user->EMAIL}', '{$request->OLD_PASSWORD}', 1, NULL, @P_REF_ID) ");
                    if($verifyOldPassword[0]->CODE != 1){
                        return response()->json(["ERROR" => "INVALID OLD PASSWORD"], 400, null, 'SP_AUTHENTICATE_LOGIN');
                    }
                    $field = 'PASSWORD';
                }elseif ($request->TYPE == 2){
                    $user = User::find($user->USER_ID);
                    $verifyOldPassword = (DB::select("SELECT FN_VERIFY_FINANCIAL_PASSWORD({$id}, '{$request->OLD_PASSWORD}') as result"))[0]->result;
                    if($verifyOldPassword == 0){
                        return response()->json(["ERROR" => "INVALID OLD PASSWORD"], 400);
                    }
                    $field = 'FINANCIAL_PASSWORD';
                }
                DB::select("UPDATE USER SET {$field} = sha2('{$request->PASSWORD}', 256) WHERE id = {$user->ID}");
                return (new Message())->defaultMessage(1, 200);

            }else{
                return response()->json(['ERROR' => ["MESSAGE" => "INVALID TYPE"]], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    //função para cotação

    public function insertCotacao($uuid, Request $request)
    {

        Validator::make($request->all(),[
            'cotacao' => 'required'
        ])->validate();

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            DB::select("INSERT INTO QUOTATION (QUOTATION, ADMIN_ID)
                VALUES ('{$request->cotacao}', '{$adm->ID}')");

            return (new Message())->defaultMessage(1, 200);

        } else {
            return (new Message())->defaultMessage(27, 404);
        }
    }

        /*
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $json = (new MassiveJsonConverter())->generate('SEARCH', $request);
            $result = DB::select("CALL SP_SEARCH_USER('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_USER');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    } */
    //fim função de cotação

    public function search($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $json = (new MassiveJsonConverter())->generate('SEARCH', $request);
            $result = DB::select("CALL SP_SEARCH_USER('{$json}', '{$uuid}', NULL, @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id == 1){
                return (new Message())->defaultMessage(1, 200, $result);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_SEARCH_USER');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function admUpdate($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $user = $this->user->find($request->ID);
        if(!$user){
            return (new Message())->defaultMessage(18, 404);
        }

        $adm = Adm::where('uuid', $uuid)->first();
        if($adm){

            $json = (new MassiveJsonConverter())->generate('UPDATE', $request);
            $result = DB::select("CALL SP_UPDATE_USER('{$json}', '{$uuid}', '', @P_CODE_LIST_ID)");
            $id = DB::select("SELECT @P_CODE_LIST_ID as id")[0]->id;
            if($id === 1){
                if ($request->has('DOCUMENT')){
                    $rr = RegistrationRequest::where('DOCUMENT', $user->DOCUMENT)->first();
                    if ($rr) DB::select("UPDATE REGISTRATION_REQUEST SET DOCUMENT = {$request->DOCUMENT} WHERE ID = {$rr->ID}");
                }
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($id, 400, null, 'SP_UPDATE_USER');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function withdrawalByToddoPay(Request $request)
    {
        Validator::make($request->all(),[
            'toddoPay' => 'required',
            'user_id' => 'required'
        ])->validate();

        $user = $this->user->find($request->user_id);
        if($user){
            DB::select("UPDATE USER SET WITHDRAWAL_BY_TUDDO_PAY = {$request->toddoPay}, DT_ACTIVE_TUDDO_PAY = NOW() WHERE ID = {$request->user_id}");
            return (new Message())->defaultMessage(1, 200);
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function export()
    {
        $data = User::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $today = date('Y-m-d');

        $sheet->setCellValue('A'.'1', "ID");
        $sheet->setCellValue('B'.'1', "EMAIL");
        $sheet->setCellValue('C'.'1', "NAME");
        $sheet->setCellValue('D'.'1', "DDI");
        $sheet->setCellValue('E'.'1', "PHONE");

        foreach ($data as $key => $dt){
            $sheet->setCellValue('A'.($key+2), $dt->ID);
            $sheet->setCellValue('B'.($key+2), $dt->EMAIL);
            $sheet->setCellValue('C'.($key+2), $dt->NAME);
            $sheet->setCellValue('D'.($key+2), $dt->DDI);
            $sheet->setCellValue('E'.($key+2), $dt->PHONE);
        }

        $writer = new Xlsx($spreadsheet);

        $name = "Users_$today.xlsx";
        $writer->save('storage/exports/'.$name);;
        return response()->file('storage/exports/Users_'.$today.'.xlsx', [ 'Content-Disposition' => 'inline; filename="Users_'.$today.'.xlsx"']);

    }
}
