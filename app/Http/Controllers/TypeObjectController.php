<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\TypeObject;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TypeObjectController extends Controller
{
    private $object;

    public function __construct(TypeObject $object)
    {
        $this->object = $object;
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
                    $objects = $this->object->where('ACTIVE', 1)->get();
                }elseif($request->status === 'inactive'){
                    $objects = $this->object->where('ACTIVE', 0)->get();
                }else{
                    return response()->json(['ERROR' => ['MESSAGE' => 'STATUS FOR PERMISSION NOT FOUND, ONLY ALLOWED ACTIVE OR INACTIVE']], 400);
                }
            }else{
                $objects = $this->object->all();
            }
            return (new Message())->defaultMessage(1, 200, $objects);
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
            $object = $this->object->find($id);
            if($object){
                return (new Message())->defaultMessage(1, 200, $object);
            }else{
                return (new Message())->defaultMessage(17, 404);
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
                'DESCRIPTION' => 'required',
            ])->validate();

            $object = $this->object->create($request->all());
            if($object){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(17, 400);
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

            Validator::make($request->all(), [
                'DESCRIPTION' => 'required',
            ])->validate();

            $object = $this->object->find($id);
            if($object){
                DB::select("UPDATE TYPE_OBJECT SET DESCRIPTION = '{$request->DESCRIPTION}' WHERE ID = {$object->ID}");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(17, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function activeOrInactive($id, $uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if((new JwtValidation())->validateByAdm($adm->ID, $request) == false){
                return (new Message())->defaultMessage(41, 403);
            }
            $object = $this->object->find($id);
            if($object){
                if($object->ACTIVE === 1){
                    DB::select("UPDATE TYPE_OBJECT SET ACTIVE = 0 WHERE ID = {$object->ID}");
                }else{
                    DB::select("UPDATE TYPE_OBJECT SET ACTIVE = 1 WHERE ID = {$object->ID}");
                }
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(65, 404);
            }

        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
