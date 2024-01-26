<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'PRODUCT';
    public $timestamps = false;
    protected $fillable = ['ID', 'NAME', 'DESCRIPTION', 'POINTS', 'GET_CASHBACK', 'PROFIT_SHARING_QUOTA', 'ACTIVE',
        'DT_REGISTER', 'ADM_ID', 'ADM_LAST_UPDATE_ADM'];

    public static function getComboName($id)
    {
        $product = self::find($id);
        if (!$product) return false;
        return $product->NAME;
    }

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
