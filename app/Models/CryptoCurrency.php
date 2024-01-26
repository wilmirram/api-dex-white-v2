<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoCurrency extends Model
{
    protected $table = 'CRYPTO_CURRENCY';
    public $timestamps = false;
    protected $fillable = ['ID', 'NAME', 'SYMBOL', 'ACTIVE', 'DT_REGISTER'];
}
