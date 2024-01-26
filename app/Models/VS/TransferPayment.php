<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class TransferPayment extends Model
{
    protected $table = 'VS_TRANSFER_PAYMENT';
    protected $fillable = [
      'ID', 'DIGITAL_PLATFORM_ID', 'HASH', 'DT_REGISTER', 'DT_TRANSFER', 'ID_TRANSFER', 'TRANSFER_TO', 'AMOUNT',
        'USER_ACCOUNT_ID', 'VS_ORDER_ID', 'ADM_ID', 'DT_LAST_UPDATE'
    ];
    public $timestamps = false;
}
