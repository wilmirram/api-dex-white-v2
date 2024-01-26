<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = 'VS_PRODUCT_PRICE';
    protected $fillable = [
        'ID', 'DT_REGISTER', 'VS_PRODUCT_ID', 'UNIT_PRICE', 'PROFIT_PERCENTAGE', 'DISCOUNT_PERCENTAGE',
        'SCORE', 'ADM_ID', 'ACTIVE'
    ];
    public $timestamps = false;

}
