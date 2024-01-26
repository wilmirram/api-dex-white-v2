<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceCategory extends Model
{
    protected $table = 'FINANCE_CATEGORY';
    protected $fillable = ['ID', 'DESCRIPTION', 'TYPE_FINANCE_CATEGORY_ID', 'ACTIVE', 'DT_REGISTER'];
    public $timestamps = false;

    public function typeFinanceCategory()
    {
        return $this->belongsTo('App\Models\TypeFinanceCategory', 'TYPE_FINANCE_CATEGORY_ID');
    }
}
