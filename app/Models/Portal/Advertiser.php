<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    protected $connection = 'mysql_portal';
    protected $table = 'ADVERTISER';
    protected $fillable = [
        'EMAIL',
        'TYPE_PERSON_ID',
        'TYPE_DOCUMENT_ID',
        'DOCUMENT',
        'NAME',
        'REPRESENTATIVE',
        'SOCIAL_REASON',
        'FANTASY_NAME',
        'COUNTRY_ID',
        'ZIP_CODE',
        'ADDRESS',
        'NUMBER',
        'COMPLEMENT',
        'NEIGHBORHOOD',
        'CITY',
        'STATE',
        'DDI',
        'PHONE',
        'DT_REGISTER',
        'ACTIVE',
        'ADM_ID',
        'DT_LAST_UPDATE_ADM',
        'NOTE'
    ];
    public $timestamps = false;
}
