<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLevel extends Model
{
    protected $fillable = ['ID', 'DESCRIPTION', 'ACTIVE', 'DT_REGISTER'];
    public $timestamps = false;
    protected $table = 'ACCESS_LEVEL';
}
