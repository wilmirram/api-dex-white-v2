<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBackWeek extends Model
{
    protected $table = 'CASHBACK_WEEK';
    public $timestamps = false;
    protected $fillable = ['ID', 'DT_REGISTER', 'DT_START', 'DT_END', 'ACTIVE'];

}
