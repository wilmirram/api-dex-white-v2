<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class CreditCardLog extends Model
{
    protected $table = 'CREDIT_CARD_LOG';
    protected $fillable = ['CREDIT_CARD_OPERATOR_ID', 'CREDIT_CARD_TRANSACTION_ID', 'CREDIT_CARD_TRANSACTION_JSON', 'VS_ORDER_ID', 'ORDER_ITEM_ID', 'CREDIT_CARD_STATUS_ID', 'ADM_ID', 'CODE_LIST_ID'];
    public $timestamps = false;
}
