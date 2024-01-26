<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    protected $table = 'PAYMENT_ORDER';
    public $timestamps = false;
    protected $fillable = ["ID", "ORDER_ITEM_ID", "PAYMENT_METHOD_ID", "DT_PAYMENT", "REFERENCE", "AMOUNT_RECEIVED", "TOKEN"];

    public function orderItem()
    {
        return $this->belongsTo('App\Models\OrderItem', 'ORDER_ITEM_ID');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'PAYMENT_METHOD_ID');
    }
}
