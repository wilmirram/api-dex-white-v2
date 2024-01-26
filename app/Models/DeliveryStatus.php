<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryStatus extends Model
{
    protected $table = 'DELIVERY_STATUS';
    protected $fillable =  ['DESCRIPTION', 'ACTIVE'];
    public $timestamps = false;
}
