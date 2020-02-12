<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class transporterRequest extends FormRequest
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
            'transporter_name' => 'required | string',
            'email' => 'required | string| email:true',
            'phone_no' => 'required |string',
            'address' => 'required | string'
        ];
    }
}
