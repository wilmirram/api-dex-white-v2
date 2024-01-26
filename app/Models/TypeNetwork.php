<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeNetwork extends Model
{
    protected $table = 'TYPE_NETWORK';
    public $timestamps = false;
    protected $fillable = ['ID', 'CHILDREN', 'DESCRIPTION'];
}
