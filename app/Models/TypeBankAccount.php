<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeBankAccount extends Model
{
    protected $table = 'TYPE_BANK_ACCOUNT';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION'];
}
