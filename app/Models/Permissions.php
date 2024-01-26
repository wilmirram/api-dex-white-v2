<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $table = 'OBJECT';
    protected $fillable = ['ID', 'NAME', 'DESCRIPTION', 'NAME_PT_BRL', 'MENU', 'TYPE_OBJECT_ID', 'DT_REGISTER', 'ACTIVE'];
    public $timestamps = false;
}
