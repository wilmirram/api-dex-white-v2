<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'CURRENCY';
    public $timestamps = false;
    protected $fillable = ['ID', 'NAME', 'SYMBOL', 'ACTIVE'];
}
