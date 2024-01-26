<?php

namespace App\Http\Controllers;

use App\Models\AccessLevel;
use App\Models\Adm;
use App\Utils\Message;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessLevelController extends Controller
{
    private $accessLevel;

    public function __construct(AccessLevel $accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }

    public function index($uuid, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            if($request->has('status')){
                if($request->status == 'active'){
                    $result = $this->accessLevel->where('ACTIVE', 1)->get(['ID', 'DESCRIPTION', 'ACTIVE']);
                }elseif ($request->status == 'inactive'){
                    $result = $this->accessLevel->where('ACTIVE', 0)->get(['ID', 'DESCRIPTION', 'ACTIVE']);
                }else{
                    $result = $this->accessLevel->get(['ID', 'DESCRIPTION', 'ACTIVE']);
                }
            }else{
                $result = $this->accessLevel->get(['ID', 'DESCRIPTION', 'ACTIVE']);
            }
            return (new Message())->defaultMessage(1, 200, $result);
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function create($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required',
        ])->validate();
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $access = $this->accessLevel->create([
                'DESCRIPTION' => $request->DESCRIPTION,
                'ACTIVE' => 1
            ]);
            if($access){
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(66, 400);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function update($uuid, $id, Request $request)
    {
        Validator::make($request->all(), [
            'DESCRIPTION' => 'required',
        ])->validate();
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $access = $this->accessLevel->find($id);
            if($access){
                DB::select("UPDATE ACCESS_LEVEL SET DESCRIPTION = '{$request->DESCRIPTION}' WHERE ID = {$id}");
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(17, 200);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }

    public function changeStatus($uuid, $id, Request $request)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if($adm){
            $access = $this->accessLevel->find($id);
            if($access){
                if($access->ACTIVE == 1){
                    DB::select("UPDATE ACCESS_LEVEL SET ACTIVE = 0 WHERE ID = {$id}");
                }elseif($access->ACTIVE == 0){
                    DB::select("UPDATE ACCESS_LEVEL SET ACTIVE = 1 WHERE ID = {$id}");
                }else{
                    return (new Message())->defaultMessage(1, 200);
                }
                return (new Message())->defaultMessage(1, 200);
            }else{
                return (new Message())->defaultMessage(17, 200);
            }
        }else{
            return (new Message())->defaultMessage(27, 404);
        }
    }
}
