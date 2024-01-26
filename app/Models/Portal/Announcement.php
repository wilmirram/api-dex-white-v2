<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $connection = 'mysql_portal';
    protected $table = 'ANNOUNCEMENT';
    protected $fillable = [
        'ADVERTISER_ID',
        'DT_REGISTER',
        'ADVERTISING_LOCATION_ID',
        'ADM_ID',
        'DT_EXPIRATION',
        'TITLE',
        'DESCRIPTION',
        'URL',
        'ACTIVE'
    ];
    public $timestamps = false;
}
