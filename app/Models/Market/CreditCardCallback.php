<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class CreditCardCallback extends Model
{
    protected $fillable = ['CREDIT_CARD_OPERATOR_ID', 'CREDIT_CARD_TRANSACTION_ID', 'CREDIT_CARD_TRANSACTION_JSON', 'ADM_ID'];
    protected $table = 'CREDIT_CARD_CALLBACK';
    public $timestamps = false;
}
