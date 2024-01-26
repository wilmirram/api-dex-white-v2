<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "P_ADM_ID" => 'required',
            "P_ORDER_ITEM_ID" => 'required',
            "P_USER_ACCOUNT_ID" => 'required',
            "P_PAYMENT_METHOD_ID" => 'required'
          //  "P_AMOUNT_RECEIVED" => 'required'
        ];
    }
}
