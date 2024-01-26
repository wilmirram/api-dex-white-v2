<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'VS_ORDER_ITEM';
    protected $fillable = [
        'ID', 'VS_ORDER_ID', 'USER_ACCOUNT_ID', 'SEQ', 'VS_PRODUCT_ID', 'VS_PRODUCT_PRICE_ID', 'UNITS', 'ADM_ID',
        'DT_LAST_UPDATE_ADM', 'DT_REGISTER', 'DT_LAST_UPDATE', 'ACTIVE'
    ];
    public $timestamps = false;
}
