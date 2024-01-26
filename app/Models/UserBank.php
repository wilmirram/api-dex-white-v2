<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBank extends Model
{
    protected $table = 'USER_BANK';
    public $timestamps = false;
    protected $fillable =["ID", "USER_ID", "SEQ", "BANK_ID", "TYPE_BANK_ACCOUNT_ID", "AGENCY", "CURRENT_ACCOUNT", "OPERATION",
        "ACTIVE", "DT_REGISTER"];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'USER_ID');
    }

    public function typeBankAccount()
    {
        return $this->belongsTo('App\Models\TypeBankAccount', 'TYPE_BANK_ACCOUNT_ID');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'BANK_ID');
    }
}
