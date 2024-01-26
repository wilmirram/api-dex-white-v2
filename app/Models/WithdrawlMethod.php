<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawlMethod extends Model
{
    protected $table = 'WITHDRAWAL_METHOD';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION'];
}
