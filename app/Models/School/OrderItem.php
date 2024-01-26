<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'SC_ORDER_ITEM';
    protected $connection = 'mysql_school';
    public $timestamps = false;
    protected $fillable = [
        'SC_ORDER_ID',
        'USER_ACCOUNT_ID',
        'USER_ID',
        'COURSE_ID',
        'COURSE_PRICE_ID'
    ];
}
