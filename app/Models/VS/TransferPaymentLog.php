<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class TransferPaymentLog extends Model
{
    protected $table = 'VS_TRANSFER_PAYMENT_LOG';
    protected $fillable = [
      'ID', 'DIGITAL_PLATFORM_ID', 'HASH', 'USER_ACCOUNT_ID', 'VS_ORDER_ID', 'CODE_LIST_ID', 'TRANSFER_TO',
        'TRANSFER_AMOUNT', 'DT_REGISTER'
    ];
    public $timestamps = false;
}
