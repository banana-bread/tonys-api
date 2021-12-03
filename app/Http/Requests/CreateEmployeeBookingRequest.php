<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeBookingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event'          => 'required|array',
            'event.start'    => 'required|date', 
            'services'       => 'required|array|min:1',
            'services.*.id'  => 'required|uuid',

        ];
    }

    public function authorize() { return true; }
}
