<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class clientRequest extends FormRequest
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
            'company_name' => 'required | string',
            'person_of_contact' => 'required | string',
            'phone_no' => 'required | string',
            'email' => 'required | string | email:true',
            'country_id' => 'required | integer',
            'state_id' => 'required | integer',
            'address' => 'required | string' 
        ];
    }
}
