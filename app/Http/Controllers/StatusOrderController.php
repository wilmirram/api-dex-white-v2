<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusOrderRequest;
use App\Models\StatusOrder;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusOrderController extends Controller
{
    private $order;

    public function __construct(StatusOrder $order)
    {
        $this->order = $order;
    }

    public function index()
    {
        $data = $this->order->all();
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function show($id)
    {
        $data = $this->order->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(StatusOrderRequest $request)
    {
        $data = $this->order->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->order->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE STATUS_ORDER SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
