<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeNetworkConfigRequest;
use App\Models\TypeNetworkConfig;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeNetworkConfigController extends Controller
{
    private $network;

    public function __construct(TypeNetworkConfig $network)
    {
        $this->network = $network;
    }

    public function index()
    {
        $data = $this->network->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->network->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(TypeNetworkConfigRequest $request)
    {
        $data = $this->network->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->network->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE TYPE_NETWORK_CONFIG SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
