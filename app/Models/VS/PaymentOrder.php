<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    protected $table = 'VS_PAYMENT_ORDER';
    protected $fillable = [
        'ID', 'VS_ORDER_ID', 'USER_ACCOUNT_ID', 'PAYMENT_METHOD_ID', 'DT_PAYMENT', 'AMOUNT_RECEIVED', 'ACTIVE',
        'NOTE', 'ADM_ID', 'DT_LAST_UPDATE_ADM'
    ];
    public $timestamps = false;
}
