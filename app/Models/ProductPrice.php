<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = 'PRODUCT_PRICE';
    public $timestamps = false;
    protected $fillable = ['ID', 'PRODUCT_ID', 'CURRENCY_ID', 'SEQ', 'PRICE', 'DAILY_GAIN_LIMIT', 'DT_REGISTER', 'ACTIVE',
        'ADM_ID', 'DT_LAST_UPDATE_ADM'];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'PRODUCT_ID');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'CURRENCY_ID');
    }

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
