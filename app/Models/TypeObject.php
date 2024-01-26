<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeObject extends Model
{
    protected $table = 'TYPE_OBJECT';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION', 'DT_REGISTER', 'ACTIVE'];
}
