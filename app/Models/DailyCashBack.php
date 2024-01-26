<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCashBack extends Model
{
    public $timestamps = false;
    protected $table = 'DAILY_CASHBACK';
    protected $fillable = ['ID', 'DT_CASHBACK', 'TICKET', 'DT_START', 'DT_END', 'LAUNCHED_ITEMS', 'BALANCE_CASHBACK', 'DT_REGISTER',
        'ACTIVE', 'ADM_ID', 'DT_LAST_UPDATE_ADM'];

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
