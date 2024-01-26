<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'VS_SUB_CATEGORY';
    protected $fillable = [
        'ID', 'VS_CATEGORY_ID', 'DESCRIPTION', 'ACTIVE'
    ];
    public $timestamps = false;
}
