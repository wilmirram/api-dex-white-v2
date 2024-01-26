<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ID' => 'required',
            'NAME' => 'required',
            'COUNTRY_ID' => 'required'
        ];
    }
}
