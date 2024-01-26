<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserWalletRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "USER_ID" => 'required',
            "CRYPTO_CURRENCY_ID" => 'required',
            "ADDRESS" => 'required',
            //"DESCRIPTION" => 'required'
        ];
    }
}
