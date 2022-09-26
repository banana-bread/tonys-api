<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
            'services'      => 'required|array|min:1',
            'services.*.id' => 'required|uuid',
            'client.id'     => 'required',
            'time_slot_id'  => 'required|integer',
        ];
    }
}
