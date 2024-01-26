<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPriceRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'PRODUCT_ID' => 'required',
            'CURRENCY_ID' => 'required',
            'PRICE' => 'required',
            'DAILY_GAIN_LIMIT' => 'required'
        ];
    }
}
