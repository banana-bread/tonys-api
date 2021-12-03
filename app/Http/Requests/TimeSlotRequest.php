<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeSlotRequest extends FormRequest
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
            'service-definition-ids' => 'required|string',
            'employee-id'            => 'present',
            'date-from'              => 'required|numeric',
            'date-to'                => 'required|numeric'
        ];
    }
}
