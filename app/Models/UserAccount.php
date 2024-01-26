<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{

    protected $fillable = ["ID", "UUID", "USER_ID", "SEQ", "NICKNAME", "PARENT_ID", "SPONSOR_ID", "SIDE", "PREFERENTIAL_SIDE", "ACTIVE",
        "DT_REGISTER", "ADM_ID", "DT_LAST_UPDATE_ADM", "NOTE"];
    protected $table = 'USER_ACCOUNT';
    public $timestamps = false;

    //TODO Arrumar modelo conforme o RegistrationRequest
}
