<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\VS\TransferPaymentLog;
use Illuminate\Http\Request;

class TransferPaymentLogController extends Controller
{
    private $log;

    public function __construct(TransferPaymentLog $log)
    {
        $this->log = $log;
    }
}
