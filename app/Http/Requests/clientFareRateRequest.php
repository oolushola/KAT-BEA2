<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class clientFareRateRequest extends FormRequest
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
            'from_state_id' => 'required | integer',
            'to_state_id' => 'required | integer',
            'destination' => 'required | string',
            'tonnage' => 'required | integer',
            'amount_rate' => 'required | between:0,99.99'
        ];
    }
}
