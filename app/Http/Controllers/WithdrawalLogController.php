<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\WithdrawalLog;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;

class WithdrawalLogController extends Controller
{
    private $log;

    public function __construct(WithdrawalLog $log)
    {
        $this->log = $log;
    }
}
