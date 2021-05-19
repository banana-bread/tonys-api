<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                                    => 'required|string',
            'address'                                 => 'required|string',
            'phone'                                   => 'required|phone:CA',
            // 'time_slot_duration'                      => 'required|numeric',
            // 'booking_grace_period'                    => 'required|numeric',

            'user'                                    => 'required|array',
            'user.name'                               => 'required|string',
            'user.email'                              => 'required|email',
            'user.phone'                              => 'required|phone:CA',
            'user.password'                           => 'required|string',

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

    // TODO: should maybe do this somewhere else
    protected function passedValidation()
    {
        $this->merge([
            'time_slot_duration' => 1800,    // 30 minutes
            'booking_grace_period' => 86400, //24 hours
        ]);
    }

    public function authorize(){return true;}
}
