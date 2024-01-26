<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\TransferPayment;
use Illuminate\Http\Request;

class TransferPaymentController extends Controller
{
    private $transfer;

    public function __construct(TransferPayment $transfer)
    {
        $this->transfer = $transfer;
    }
}
