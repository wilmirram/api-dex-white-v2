<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'GENRE';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION'];
}
