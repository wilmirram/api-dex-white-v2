<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    private $payment;

    public function __construct(PaymentMethod $payment)
    {
        $this->payment = $payment;
    }

    public function index()
    {
        $data = $this->payment->where('ACTIVE', 1)->get();
        return (new Message())->defaultMessage(1, 200, $data);
    }

    public function show($id)
    {
        $data = $this->payment->find($id);
        if($data){
            return response()->json($data);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function store(PaymentMethodRequest $request)
    {
        $data = $this->payment->create($request->all());
        if($data){
            return (new Message())->defaultMessage(21, 200, $data);
        }else{
            return (new Message())->defaultMessage(20, 500);
        }
    }

    public function update($id, Request $request)
    {
        $data = $this->payment->find($id);
        if($data){
            foreach ($request->all() as $key => $value) {
                DB::select("UPDATE PAYMENT_METHOD SET {$key} = '{$value}' WHERE id = {$id}");
            }
            return (new Message())->defaultMessage(22, 203);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }

    public function paymentMethodOffice()
    {
        $result = DB::select("SELECT PM.ID,
                                     PM.DESCRIPTION
                              FROM PAYMENT_METHOD PM
                             WHERE PM.ACTIVE
                               AND PM.OFFICE_ON");
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function paymentMethodStore()
    {
        $result = DB::select("SELECT PM.ID,
                                     PM.DESCRIPTION
                                      FROM PAYMENT_METHOD PM
                                     WHERE PM.ACTIVE
                                       AND PM.MARKET_ON");
        return (new Message())->defaultMessage(1, 200, $result);
    }
}
