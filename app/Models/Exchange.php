<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $table = 'EXCHANGE';
    public $timestamps = false;
    protected $fillable = ['NAME', 'URL', 'ADM_ID'];
}
