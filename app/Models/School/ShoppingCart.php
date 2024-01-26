<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    protected $table = 'SHOPPING_CART';
    protected $connection = 'mysql_school';
    public $timestamps = false;
    protected $fillable = [
        'USER_ID', 'USER_ACCOUNT_ID', 'COURSE_ID', 'COURSE_PRICE_ID', 'ACTIVE', 'DT_REGISTER', 'DT_LAST_UPDATE'
    ];
}
