<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequestRequest extends FormRequest
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


    public function rules()
    {
        //AS RESPONSES ESTÃO BUGADAS POIS OS CAMPOS SÃO ESCRITOS TODOS EM CAPS LOCK
        return [
            'P_EMAIL' => 'required|email',
            'P_NAME' => 'required',
            'P_SPONSOR_NICKNAME' => 'required',
            'P_NICKNAME' => 'required',
            'P_TYPE_DOCUMENT_ID' => 'required',
            'P_DOCUMENT' => 'required',
            //'P_PREFERENTIAL_SIDE' => 'required',
            //'P_REGISTRATION_REQUEST_ID' => 'required'
        ];
    }
}
