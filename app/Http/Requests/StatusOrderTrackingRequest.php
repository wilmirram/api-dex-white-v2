<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusOrderTrackingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'DESCRIPTION' => 'required'
        ];
    }
}
