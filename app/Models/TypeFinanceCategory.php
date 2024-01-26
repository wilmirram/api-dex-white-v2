<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeFinanceCategory extends Model
{
    protected $table = 'TYPE_FINANCE_CATEGORY';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION', 'SYMBOL'];
}
