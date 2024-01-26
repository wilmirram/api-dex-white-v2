<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    protected $table = 'VS_VARIATION';
    protected $fillable = ['VARIATION', 'ADM_ID'];
    public $timestamps = false;
}
