<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'ID', 'USER_ID', 'USER_ACCOUNT_ID', 'GLOSS_PRICE', 'FEE', 'SHIPPING_COST', 'DISCOUNT', 'NET_PRICE', 'PURCHASE_SCORE',
        'LAUNCHED_SCORE', 'PAYMENT_METHOD_ID', 'PAYMENT_VOUCHER', 'DT_PAYMENT_VOUCHER', 'STATUS_ORDER_ID', 'DIGITAL_PLATFORM_ID',
        'BILLET_ID', 'BILLET_DIGITABLE_LINE', 'BILLET_URL_PDF', 'BILLET_FEE', 'BILLET_NET_PRICE', 'BILLET_DATE', 'COUNTRY_ID',
        'ZIP_CODE', 'ADDRESS', 'NUMBER', 'COMPLEMENT', 'NEIGHBORHOOD', 'CITY', 'STATE', 'DT_REGISTER', 'DT_LAST_UPDATE',
        'ADM_ID', 'DT_LAST_UPDATE_ADM', 'ACTIVE'
    ];

    protected $table = 'VS_ORDER';
    public $timestamps = false;
}
