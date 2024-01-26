<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use App\Models\VS\UserAddress;
use App\Models\User;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    private $user;

    public function __construct(UserAddress $user)
    {
        $this->user = $user;
    }

    public function show($id, Request $request)
    {
        $user = UserAccount::find($id);
        if($user){

            if((new JwtValidation())->validateByUserAccount($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $result = DB::select("CALL SP_GET_USER_ADDRESS_LIST($id)");
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(13, 404);
        }
    }

    public function externalShow($id, Request $request)
    {
        $user = User::find($id);
        if(!$user) return (new Message())->defaultMessage(13, 404);
        if((new JwtValidation())->validateByUser($user->ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        $result = DB::select("CALL SP_GET_USER_ADDRESS_LIST_BY_USER_ID($id)");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required',
            'COUNTRY_ID' => 'required',
            'ZIP_CODE' => 'required',
            'ADDRESS' => 'required',
            'NUMBER' => 'required',
            'NEIGHBORHOOD' => 'required',
            'CITY' => 'required',
            'STATE' => 'required'
        ])->validate();

        $user = User::find($request->USER_ID);
        if($user){
            if((new JwtValidation())->validateByUser($user->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            foreach ($request->all() as $key => $value) {
                if ($value != "") {
                    $data[$key] = strtoupper($value);
                }
            }

            $result = $this->user->create($data);
            if($result){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return response()->json(['ERROR' => 'THERE WAS AN ERROR SAVING DATA'], 400);
            }
        }else{
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function update($id, Request $request)
    {
        $address = $this->user->find($id);
        if($address){
            if((new JwtValidation())->validateByUser($address->USER_ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            foreach ($request->all() as $key => $value) {
                if($value != ""){
                    $value = strtoupper($value);
                    DB::select("UPDATE USER_ADDRESS SET {$key} = UPPER('{$value}') WHERE id = {$id}");
                }
            }
            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => 'USER ADDRESS NOT FOUND'], 404);
        }
    }

    public function delete($id, Request $request)
    {
        $address = $this->user->find($id);
        if($address){
            if((new JwtValidation())->validateByUser($address->USER_ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            DB::select("DELETE FROM USER_ADDRESS WHERE id = {$id}");
            return (new Message())->defaultMessage(1, 200);
        }else{
            return response()->json(['ERROR' => 'USER ADDRESS NOT FOUND'], 404);
        }
    }

    public function chageStatus($id, Request $request)
    {
        $address = $this->user->find($id);
        if(!$address) return response()->json(['ERROR' => 'USER ADDRESS NOT FOUND'], 404);
        if((new JwtValidation())->validateByUser($address->USER_ID, $request) == false) return (new Message())->defaultMessage(41, 403);

        $status = 0;
        if ($address->ACTIVE == 0) $status = 1;

        DB::select("UPDATE USER_ADDRESS SET ACTIVE = {$status} WHERE ID = {$id}");
        return (new Message())->defaultMessage(1, 200);
    }
}
