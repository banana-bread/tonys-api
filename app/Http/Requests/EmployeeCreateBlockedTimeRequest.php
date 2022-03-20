<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeCreateBlockedTimeRequest extends FormRequest
{
    public function authorize(){ return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_date' => 'required|date',
            'end_date'   => 'date',
            'start_time' => 'required_if:is_all_day,false',
            'end_time'   => 'required_if:is_all_day,false',
            'is_all_day' => 'required|boolean',
        ];
    }
}
