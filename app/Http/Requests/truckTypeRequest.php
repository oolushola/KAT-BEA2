<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class truckTypeRequest extends FormRequest
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
            'truck_type_code' => 'required | string | max:3',
            'truck_type' => 'required | string ',
            'tonnage' => 'required | integer'
        ];
    }
}
