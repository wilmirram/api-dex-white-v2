<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $table = 'USER_WALLET';
    protected $fillable = ["ID", "USER_ID", "SEQ", "CRYPTO_CURRENCY_ID", "ADDRESS", "ACTIVE", "DT_REGISTER", "DT_LAST_UPDATE"];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'USER_ID');
    }

    public function criptocoin()
    {
        return $this->belongsTo('App\Models\CryptoCurrency', 'CRYPTO_CURRENCY_ID');
    }
}
