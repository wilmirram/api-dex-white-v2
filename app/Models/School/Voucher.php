<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $connection = 'mysql_school';
    protected $table = 'VOUCHER';
    public $timestamps = false;
    protected $fillable = [
        'VOUCHER',
        'PRODUCT_ID',
        'DT_REGISTER',
        'ORDER_ITEM_ID',
        'RECEIVER_USER_ID',
        'RECEIVER_USER_ACCOUNT_ID',
        'AVAILABLE',
        'DT_OF_USE',
        'DT_LAST_UPDATE'
    ];
}
