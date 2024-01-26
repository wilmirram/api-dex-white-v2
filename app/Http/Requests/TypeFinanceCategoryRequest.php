<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TypeFinanceCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'DESCRIPTION' => 'required',
            'SYMBOL' => 'required'
        ];
    }
}
