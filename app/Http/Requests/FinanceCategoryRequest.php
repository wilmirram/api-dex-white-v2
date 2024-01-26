<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceCategoryRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'DESCRIPTION' => 'required',
            'TYPE_FINANCE_CATEGORY_ID' => 'required'
        ];
    }
}
