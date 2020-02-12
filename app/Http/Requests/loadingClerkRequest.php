<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class loadingClerkRequest extends FormRequest
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
            'first_name' => 'required | string | max:90',
            'last_name' => 'required | string | max:90',
            'phone_no' => 'required | string',
            'email' => 'required | email:true | string',
            'location_id' => 'required | integer',
            'field_ops_type' => 'required | integer',
            'address' => 'required | string'
        ];
    }
}
