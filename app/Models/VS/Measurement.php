<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    protected $fillable = ['ID', 'DESCRIPTION', 'SYMBOL'];
    protected $table = 'VS_MEASUREMENT';
    public $timestamps = false;
}
