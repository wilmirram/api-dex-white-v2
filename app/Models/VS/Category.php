<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['ID', 'DESCRIPTION', 'COMMISSION_PERCENTAGE_SCORE'];
    public $timestamps = false;
    protected $table = 'VS_CATEGORY';
}
