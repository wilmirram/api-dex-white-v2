<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithDrawalRequestSheet extends Model
{
    public $timestamps = false;
    protected $table = 'WITHDRAWAL_REQUEST_SHEET';
    protected $fillable = ['FILENAME', 'BALANCE_TOTAL', 'ADM_ID', 'DT_REGISTER'];
}
