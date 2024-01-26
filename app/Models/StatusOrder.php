<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusOrder extends Model
{
    protected $table = 'STATUS_ORDER';
    public $timestamps = false;
    protected $fillable = ['ID', 'STATUS', 'DT_REGISTER', 'ACTIVE'];
}
