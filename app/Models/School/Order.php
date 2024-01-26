<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'SC_ORDER';
    protected $connection = 'mysql_school';
    public $timestamps = false;
    protected $fillable = [
        'USER_ACCOUNT_ID',
        'USER_ID',
        'GLOSS_PRICE',
        'FEE',
        'SHIPPING_COST',
        'DISCOUNT',
        'NET_PRICE',
        'STATUS_ORDER_ID',
        'COUNTRY_ID',
        'ZIP_CODE',
        'ADDRESS',
        'NUMBER',
        'CITY',
        'NEIGHBORHOOD',
        'STATE'
    ];
}
