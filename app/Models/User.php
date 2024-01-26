<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $table = 'USER';
    protected $fillable = ['ID', 'EMAIL', 'TYPE_PERSON_ID', 'TYPE_DOCUMENT_ID',
        'DOCUMENT', 'VERIFIED_DOCUMENT', 'DT_VERIFIED_DOCUMENT', 'DT_BIRTHDAY', 'NAME', 'REPRESENTATIVE', 'SOCIAL_REASON',
        'FANTASY_NAME', 'COUNTRY_ID', 'ZIP_CODE', 'ADDRESS', 'NUMBER', 'COMPLEMENT', 'NEIGHBORHOOD', 'CITY', 'STATE', 'DDI',
        'PHONE', 'DT_REGISTER', 'FIRST_INDICATION', 'ACTIVE', 'BLOCKED', 'ACTIVE_2FA', 'TOKEN', 'ADM_ID', 'SEQ_AUTOINCREMENT',
        'DT_LAST_UPDATE_ADM', 'NOTE', 'CAREER_PATH_USER_ACCOUNT_ID', 'DT_SET_CAREER_PATH_USER_ACCOUNT'];
    public $timestamps = false;

    //TODO Arrumar modelo conforme o RegistrationRequest

    public function typePerson()
    {
        return $this->belongsTo('App\Models\TypePerson', 'TYPE_PERSON_ID');
    }

    public function typeDocument()
    {
        return $this->belongsTo('App\Models\TypeDocument', 'TYPE_DOCUMENT_ID');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'COUNTRY_ID');
    }

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
