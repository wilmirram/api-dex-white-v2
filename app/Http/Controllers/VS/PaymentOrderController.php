<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\PaymentOrder;
use Illuminate\Http\Request;

class PaymentOrderController extends Controller
{
    private $payment;

    public function __construct(PaymentOrder $payment)
    {
        $this->payment = $payment;
    }
}
