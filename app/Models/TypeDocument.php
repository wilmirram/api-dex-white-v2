<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeDocument extends Model
{
    protected $table = 'TYPE_DOCUMENT';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION'];

}
