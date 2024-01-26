<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBonusScore extends Model
{
    public $timestamps = false;
    protected $table = 'DAILY_BONUS_SCORE';
    protected $fillable = ['ID', 'DT_BONUS', 'DT_START', 'DT_END', 'LAUNCHED_ITEMS', 'BALANCE_BONUS', 'DT_REGISTER', 'ACTIVE',
        'ADM_ID', 'DT_LAST_UPDATE_ADM'];

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
