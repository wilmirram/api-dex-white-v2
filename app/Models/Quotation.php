<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'QUOTATION';
    public $timestamps = false;
    protected $fillable = ['ID', 'QUOTATION', 'CRYPTO_CURRENCY_ID', 'ACTIVE', 'DT_REGISTER'];
}