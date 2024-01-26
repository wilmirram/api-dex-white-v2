<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeList extends Model
{
    protected $table = 'CODE_LIST';
    public $timestamps = false;
    protected $fillable = ['ID', 'REF', 'DESCRIPTION'];
}
