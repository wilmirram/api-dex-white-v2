<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusOrderRequest;
use App\Http\Requests\StatusOrderTrackingRequest;
use App\Models\StatusOrderTracking;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusOrderTrackingController extends Controller
{
    private $order;

    public function __construct(StatusOrderTracking $order)
    {
        $this->order = $order;
    }

    public function index()
    {
        $data = $this->order->all();
        return response()->json($data);
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

    public function store(StatusOrderTrackingRequest $request)
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
                DB::select("UPDATE STATUS_ORDER_TRACKING SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
