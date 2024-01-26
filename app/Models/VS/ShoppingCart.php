<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    protected $table = 'VS_SHOPPING_CART';
    public $timestamps = false;
    protected $fillable = [
        'USER_ID', 'USER_ACCOUNT_ID', 'VS_PRODUCT_ID', 'VS_PRODUCT_PRICE_ID', 'UNITS', 'ACTIVE', 'DT_REGISTER', 'VARIATIONS_JSON'
    ];
}
