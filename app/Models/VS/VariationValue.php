<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class VariationValue extends Model
{
    protected $table = 'VS_VARIATION_VALUE';
    protected $fillable = ['VS_VARIATION_ID', 'VARIATION_VALUE', 'ADM_ID'];
    public $timestamps = false;
}
