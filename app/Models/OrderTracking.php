<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $table = 'ORDER_TRACKING';
    public $timestamps = false;
    protected $fillable = ['ID', 'USER_ACCOUNT_ID', 'SEQ', 'ORDER_ITEM_ID', 'PAYMENT_ORDER_ID', 'STATUS_ORDER_TRACKING_ID',
        'UPDATED_ORDER_TRACKING_ID', 'DT_START_UPDATED', 'BOOK_BALANCE', 'DT_START', 'DT_END', 'DT_REGISTER', 'DT_LAST_UPDATE',
        'ACTIVE', 'INSERTED_SCORE', 'DT_INSERTED_SCORE', 'INSERTED_INDICATOR_BONUS', 'SPONSOR_ID', 'ADM_ID', 'DT_LAST_UPDATE_ADM'];

    public function userAccount()
    {
        return $this->belongsTo('App\Models\UserAccount', 'USER_ACCOUNT_ID');
    }

    public function orderItem()
    {
        return $this->belongsTo('App\Models\OrderItem', 'ORDER_ITEM_ID');
    }

    public function paymentOrder()
    {
        return $this->belongsTo('App\Models\PaymentOrder', 'PAYMENT_ORDER_ID');
    }

    public function statusOrderTracking()
    {
        return $this->belongsTo('App\Models\StatusOrderTracking', 'STATUS_ORDER_TRACKING_ID');
    }

    public function sponsor()
    {
        return $this->belongsTo('App\Models\UserAccount', 'SPONSOR_ID');
    }

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }

}
