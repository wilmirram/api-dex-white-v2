<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Permissions;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PermissionsController extends Controller
{
    private $permissions;

    public function __construct(Permissions $permissions)
    {
        $this->permissions = $permissions;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            if($request->has('status')){
                if($request->status === 'active') {
                    $permissions = $this->permissions->where('ACTIVE', 1)->get();
                }elseif($request->status === 'inactive'){
                    $permissions = $this->permissions->where('ACTIVE', 0)->get();
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'STATUS FOR PERMISSION NOT FOUND, ONLY ALLOWED ACTIVE OR INACTIVE']], 400);
                }
            }else{
                $permissions = $this->permissions->all();
            }
            return (new Message())->defaultMessage(1, 200, $permissions);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function show($id, $uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            $permissions = $this->permissions->find($id);
            if($permissions){
                return (new Message())->defaultMessage(1, 200, $permissions);
            }else{
                return (new Message())->defaultMessage(65, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function store($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            Validator::make($request->all(), [
                'NAME' => 'required',
                'DESCRIPTION' => 'required',
                'NAME_PT_BRL' => 'required',
                'TYPE_OBJECT_ID' => 'required'
            ])->validate();

            $permission = $this->permissions->create($request->all());
            if($permission){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(66, 400);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function update($id, $uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            if($request->has('ACTIVE') || $request->has('DT_REGISTER') || $request->has('ID')){
                return response()->json(['ERROR' => ['MESSAGE' => 'FIELDS LIKE ACTIVE, DT_REGISTER AND ID CAN NOT BE CHANGE FOR THIS WAY']], 400);
            }
            $permissions = $this->permissions->find($id);
            if($permissions){
                foreach ($request->all() as $key => $value)
                {
                    if($value != null){
                        DB::select("UPDATE OBJECT SET {$key} = '{$value}' WHERE ID = {$permissions->ID}");
                    }
                }
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(65, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function inactiveOrActive($id, $uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            if($request->has('ACTIVE') || $request->has('DT_REGISTER') || $request->has('ID')){
                return response()->json(['ERROR' => ['MESSAGE' => 'FIELDS LIKE ACTIVE, DT_REGISTER AND ID CAN NOT BE CHANGE FOR THIS WAY']], 400);
            }
            $permissions = $this->permissions->find($id);
            if($permissions){
                if($permissions->ACTIVE === 1){
                    DB::select("UPDATE OBJECT SET ACTIVE = 0 WHERE ID = {$permissions->ID}");
                }else{
                    DB::select("UPDATE OBJECT SET ACTIVE = 1 WHERE ID = {$permissions->ID}");
                }
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(65, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function getAdmPrivilegesList($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            Validator::make($request->all(), [
                'P_ACCESS_LEVEL_ID' => 'required',
            ])->validate();

            $result = DB::select("CALL SP_GET_ADM_PRIVILEGES({$request->P_ACCESS_LEVEL_ID}, '{$uuid}')");
            if(property_exists($result[0], 'CODE')){
                return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_GET_ADM_PRIVILEGES');
            }else{
                return (new Message())->defaultMessage(1, 200, $result);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function updateAdmPrivileges($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }

            Validator::make($request->all(), [
                'P_OBJECT_ID' => 'required',
                'P_TYPE_OBJECT_ID' => 'required',
                'P_OBJECT_ACCESS_LEVEL_ID' => 'required',
                'P_ACCESS_LEVEL_ID' => 'required',
                'P_ACTIVE' => 'required',
            ])->validate();

            $result = DB::select("CALL SP_UPDATE_ADM_PRIVILEGE({$request->P_OBJECT_ID}, {$request->P_TYPE_OBJECT_ID}, {$request->P_OBJECT_ACCESS_LEVEL_ID}, {$request->P_ACCESS_LEVEL_ID}, {$request->P_ACTIVE}, '{$uuid}')");
            if($result[0]->CODE == 1){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage($result[0]->CODE, 400, null, 'SP_UPDATE_ADM_PRIVILEGE');
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
