<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'VS_BRAND';
    protected $fillable = ['NAME', 'DT_REGISTER'];
    public $timestamps = false;
}
