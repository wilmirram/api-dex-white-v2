<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypePerson extends Model
{
    protected $table = 'TYPE_PERSON';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION', 'ACTIVE'];
}
