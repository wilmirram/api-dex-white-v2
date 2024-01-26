<?php

namespace App\Http\Controllers;

use App\Models\VS\Order;
use App\Utils\Paypal;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function create($id)
    {
        return view('paypal', compact('id'));
    }

    public function store($id, Request $request)
    {
        $paypal = new Paypal();
        $order = Order::find($id);
        $response = $paypal->makeTransaction($order);
        return response()->json($response);
    }

    public function confirmPayment(Request $request)
    {
        $paypal = new Paypal();
        $response = $paypal->payment($request->all());
        return response()->json($response);
    }
}
