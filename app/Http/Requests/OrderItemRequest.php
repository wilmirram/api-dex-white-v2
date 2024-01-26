<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "ID",
            "P_USER_ACCOUNT_ID" => 'required',
            "SEQ",
            "P_PRODUCT_ID" => 'required',
            "PRODUCT_PRICE_ID",
            "GLOSS_PRICE",
            "DISCOUNT",
            "NET_PRICE",
            "UPGRADE",
            "CURRENT_ORDER_ITEM_ID",
            "PRODUCT_POINTS",
            "POINTS_LAUNCHED",
            "STATUS_ORDER_ID",
            "DT_REGISTER",
            "DT_ORDER_ITEM_EXPIRATION",
            "REFERENCE",
            "ACTIVE",
            "ADM_ID",
            "DT_LAST_UPDATE_ADM"
        ];
    }
}
