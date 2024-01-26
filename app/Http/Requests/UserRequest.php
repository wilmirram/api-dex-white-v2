<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'P_SYSTEM_ID' => 'required',
            'P_REGISTRATION_REQUEST_ID' => 'required'
        ];
    }
}
