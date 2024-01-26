<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceQuoteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'REFERENCED_CURRENCY_ID' => 'required',
            'QUOTED_CURRENCY_ID' => 'required',
            'PRICE' => 'required'
        ];
    }
}
