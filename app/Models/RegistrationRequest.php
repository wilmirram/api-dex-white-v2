<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationRequest extends Model
{
    //TODO Fazer o fillable para proteção dos dados ou guards

    protected $table = 'REGISTRATION_REQUEST';
    public $timestamps = false;
    protected $fillable = ["ID", "EMAIL", "VERIFIED_EMAIL", "DT_VERIFIED_EMAIL", "SPONSOR_UUID", "SPONSOR_ID",
        "PREFERENTIAL_SIDE", "PASSWORD", "NICKNAME", "TYPE_DOCUMENT_ID", "DOCUMENT", "DT_REQUEST", "LAST_UPDATE_REQUEST",
        "SEND", "DT_SEND", "RESEND", "DT_RESEND", "REGISTER", "USER_ID", "DT_REGISTER", "ADM_ID", "DT_LAST_UPDATE_ADM", "NOTE"];

    public function userAccount()
    {
        return $this->belongsTo('App\Models\UserAccount', 'SPONSOR_ID');
    }

    public function typeDocument()
    {
        return $this->belongsTo('App\Models\TypeDocument', 'TYPE_DOCUMENT_ID');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'USER_ID');
    }

    public function adm(){
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
    /**/
}
