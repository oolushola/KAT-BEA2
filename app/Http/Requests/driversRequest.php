<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class driversRequest extends FormRequest
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
            'driver_first_name' => 'required | string',
            'driver_last_name' => 'required | string',
            'driver_phone_number' => 'required | string',
            'motor_boy_first_name' => 'required | string',
            'motor_boy_last_name' => 'required | string',
            'motor_boy_phone_no' => 'required | string'
        ];
    }
}
