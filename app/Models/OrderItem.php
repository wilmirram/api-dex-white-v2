<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'ORDER_ITEM';

    protected $primaryKey = 'ID';

    protected $fillable = ["ID", "USER_ACCOUNT_ID", "SEQ", "PRODUCT_ID", "PRODUCT_PRICE_ID", "GLOSS_PRICE", "DISCOUNT",
        "NET_PRICE", "UPGRADE", "CURRENT_ORDER_ITEM_ID", "PRODUCT_POINTS", "POINTS_LAUNCHED", "STATUS_ORDER_ID", "DT_REGISTER",
        "DT_ORDER_ITEM_EXPIRATION", "REFERENCE", "ACTIVE", "ADM_ID", "DT_LAST_UPDATE_ADM", "DIGITAL_PLATFORM_ID", "BILLET_ID",
        "BILLET_DIGITABLE_LINE", "BILLET_URL_PDF", "BILLET_NET_PRICE"];
        
    public $timestamps = false;

    public function userAccount()
    {
        return $this->belongsTo('App\Models\UserAccount', 'USER_ACCOUNT_ID');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'PRODUCT_ID');
    }

    public function productPrice()
    {
        return $this->belongsTo('App\Models\ProductPrice', 'PRODUCT_PRICE_ID');
    }

    public function currentOrderItem()
    {
        return $this->belongsTo('App\Models\OrderItem', 'CURRENT_ORDER_ITEM_ID');
    }

    public function statusOrderId()
    {
        return $this->belongsTo('App\Models\StatusOrder', 'STATUS_ORDER_ID');
    }

    public function adm()
    {
        return $this->belongsTo('App\Models\Adm', 'ADM_ID');
    }
}
