<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTrackingItem extends Model
{
    protected $table = 'ORDER_TRACKING_ITEM';
    public $timestamps = false;
    protected $fillable = ['ID', 'USER_ACCOUNT_ID', 'ORDER_ITEM_ID', 'ORDER_TRACKING_ID', 'SEQ', 'DT_REFERENCE',
        'FINANCE_CATEGORY_ID', 'CASHED', 'UPDATED', 'UPDATED_ORDER_TRACKING_ID', 'AFTER_CLOSING_ORDER', 'DT_REGISTER',
        'DT_LAST_UPDATE', 'ACTIVE', 'ADM_ID', 'DT_LAST_UPDATE_ADM'];

    public function userAccount()
    {
        return $this->belongsTo('App\Models\UserAccount', 'USER_ACCOUNT_ID');
    }

    public function orderItem()
    {
        return $this->belongsTo('App\Models\OrderItem', 'ORDER_ITEM_ID');
    }

    public function financeCategory()
    {
        return $this->belongsTo('App\Models\FinanceCategory', 'FINANCE_CATEGORY_ID');
    }

    public function orderTracking()
    {
        return $this->belongsTo('App\Models\OrderTracking', 'ORDER_TRACKING_ID');
    }

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
