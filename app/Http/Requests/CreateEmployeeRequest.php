<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
            'company_id'                              => 'required|uuid',
            'name'                                    => 'required|string',
            'email'                                   => 'required|email|unique:users',
            'phone'                                   => 'phone:CA',
            'password'                                => 'required|string',
            'admin'                                   => 'required|boolean',
            'settings'                                => 'required|array',
            'settings.base_schedule'                  => 'required|array',

            'settings.base_schedule.monday'           => 'required|array',
            'settings.base_schedule.monday.start'     => 'present',
            'settings.base_schedule.monday.end'       => 'required_with:settings.base_schedule.monday.start',

            'settings.base_schedule.tuesday'          => 'required|array',
            'settings.base_schedule.tuesday.start'    => 'present',
            'settings.base_schedule.tuesday.end'      => 'required_with:settings.base_schedule.tuesday.start',

            'settings.base_schedule.wednesday'        => 'required|array',
            'settings.base_schedule.wednesday.start'  => 'present',
            'settings.base_schedule.wednesday.end'    => 'required_with:settings.base_schedule.wednesday.start',

            'settings.base_schedule.thursday'          => 'required|array',
            'settings.base_schedule.thursday.start'    => 'present',
            'settings.base_schedule.thursday.end'      => 'required_with:settings.base_schedule.thursday.start',

            'settings.base_schedule.friday'            => 'required|array',
            'settings.base_schedule.friday.start'      => 'present',
            'settings.base_schedule.friday.end'        => 'required_with:settings.base_schedule.friday.start',

            'settings.base_schedule.saturday'          => 'required|array',
            'settings.base_schedule.saturday.start'    => 'present',
            'settings.base_schedule.saturday.end'      => 'required_with:settings.base_schedule.saturday.start',

            'settings.base_schedule.sunday'             => 'required|array',
            'settings.base_schedule.sunday.start'       => 'present',
            'settings.base_schedule.sunday.end'         => 'required_with:settings.base_schedule.sunday.start',
        ];
    }
}
