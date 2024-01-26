<?php

namespace App\Models\VS;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'USER_ADDRESS';
    protected $fillable = ['ID', 'USER_ID', 'COUNTRY_ID', 'ZIP_CODE', 'ADDRESS', 'NUMBER', 'COMPLEMENT',
        'NEIGHBORHOOD', 'CITY', 'STATE', 'ACTIVE'];
    public $timestamps = false;
}
