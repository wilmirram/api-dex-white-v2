<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "P_EMAIL" => 'required|email',
            "P_PASSWORD" => 'required',
            //"P_USER_ID" => 'required',
            "P_SPONSOR_ID" => 'required',
            "P_NICKNAME" => 'required',
            "P_SIDE" => 'required'
        ];
    }
}
