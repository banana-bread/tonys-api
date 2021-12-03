<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;


class CreateEmployeeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name'                              => 'required|string',
            'last_name'                               => 'required|string',
            'email'                                   => 'required|email|unique:users',
            'phone'                                   => 'phone:CA',
            'password'                                => 'required|string',
            'admin'                                   => 'required|boolean',
            'owner'                                   => 'required|boolean',
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

    protected function passedValidation()
    {
        if (! $this->password) { return; }

        $this->merge([
            'password' => Hash::make($this->password)
        ]);
    }

    public function authorize(){return true;}
}
