<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferBalanceAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'P_USER_ACCOUNT_ID' => 'required',
            'P_WITHDRAWAL_METHOD_ID' => 'required',
            'P_AMOUNT' => 'required',
            'P_USER_RECEIVED_BALANCE' => 'required',
            'P_FINANCIAL_PASSWORD' => 'required'

        ];
    }
}
