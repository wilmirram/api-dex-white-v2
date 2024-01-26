<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceQuote extends Model
{
    protected $table = 'PRICE_QUOTE';
    public $timestamps = false;
    protected $fillable = ['ID', 'DT_QUOTE', 'REFERENCED_CURRENCY_ID', 'QUOTED_CURRENCY_ID', 'PRICE', 'ADM_ID', 'DT_LAST_UPDATE_ADM'];

    public function referencedCurrency()
    {
        return $this->belongsTo('App\Models\Currency', 'REFERENCED_CURRENCY_ID');
    }

    public function quotedCurrency()
    {
        return $this->belongsTo('App\Models\Currency', 'QUOTED_CURRENCY_ID');
    }

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
