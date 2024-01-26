<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentOrderRequest;
use App\Models\Adm;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\PaymentOrder;
use App\Models\Product;
use App\Models\SendWhatsapp;
use App\Models\UserAccount;
use App\Models\User;
use App\Utils\Invoice;
use App\Utils\JwtValidation;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function GuzzleHttp\json_encode;
use App\Services\CryptoUsdtService;

class PaymentUsdt extends Controller
{
    public function transferirmatic()
    {
        echo('ramires');

    }
}
