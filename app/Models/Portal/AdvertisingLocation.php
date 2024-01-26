<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Model;

class AdvertisingLocation extends Model
{
    protected $connection = 'mysql_portal';
    protected $table = 'ADVERTISING_LOCATION';
    protected $fillable = [
        'LOCATION',
        'ACTIVE',
        'DT_REGISTER'
    ];
    public $timestamps = false;
}
