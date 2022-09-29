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
          'type'               => 'required|string|in:appointment,time-off',
          'started_at'         => 'required|date',
          'ended_at'           => 'required|date',
          'services'           => 'required_if:type,booking|array',
          'services*.id'       => 'required_if:type,booking|uuid',
          // 'employee.id'        => 'required|uuid',
          'manual_client_name' => 'nullable|string'
        ];
    }

    public function authorize() { return true; }
}
