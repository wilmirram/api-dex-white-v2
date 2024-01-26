<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusOrderTracking extends Model
{
    protected $table = 'STATUS_ORDER_TRACKING';
    public $timestamps = false;
    protected $fillable = ['ID', 'DESCRIPTION', 'ACTIVE'];
}
