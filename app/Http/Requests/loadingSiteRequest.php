<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class loadingSiteRequest extends FormRequest
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
            'state_domiciled' => 'required | integer',
            'loading_site_code' => 'required | string | max:3',
            'loading_site' => 'required | string',
            'address' => 'required | string'
        ];
    }
}
