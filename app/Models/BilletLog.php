<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BilletLog extends Model
{
    protected $table = 'BILLET_LOG';
    protected $fillable = [
        'ID', 'USER_ACCOUNT_ID', 'DIGITAL_PLATFORM_ID', 'ORDER_ITEM_ID', 'VS_ORDER_ID', 'BILLET_ID', 'BILLET_DIGITABLE_LINE',
        'BILLET_URL_PDF', 'BILLET_FEE', 'BILLET_NET_PRICE', 'BILLET_DATE', 'BILLET_DELETE', 'DT_REGISTER'
    ];
    public $timestamps = false;
}
