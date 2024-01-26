<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithDrawlRequest extends Model
{
    protected $table = 'WITHDRAWAL_REQUEST';
    public $timestamps = false;
    protected $fillable = ['ID', 'USER_ACCOUNT_ID', 'USER_ID', 'SEQ', 'DT_REGISTER', 'GLOSS_AMOUNT', 'FEE_AMOUNT', 'NET_AMOUNT',
        'WITHDRAWAL_STATUS_ID', 'WITHDRAWAL_METHOD_ID', 'USER_BANK_ID', 'BANK_ID', 'TYPE_BANK_ACCOUNT_ID', 'AGENCY', 'CURRENCY_ACCOUNT',
        'OPERATION', 'USER_WALLET_ID', 'CRYPTO_CURRENCY_ID', 'ADDRESS', 'FINANCE_CATEGORY_ID', 'DT_DEPOSIT', 'TOKEN', 'REFERENCE',
        'NOTE', 'DT_LAST_UPDATE', 'ADM_ID', 'DT_LAST_UPDATE_ADM'];
}
