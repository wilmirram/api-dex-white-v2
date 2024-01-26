<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TypeNetworkConfigRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'DESCRIPTION' => 'required',
            'TYPE_NETWORK_ID' => 'required'
        ];
    }
}
