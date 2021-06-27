<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyBaseScheduleRequest extends FormRequest
{
    public function authorize(){return true;}

    public function rules()
    {
        return [
            'base_schedule'                  => 'required|array',

            'base_schedule.monday'           => 'required|array',
            'base_schedule.monday.start'     => 'present',
            'base_schedule.monday.end'       => 'required_with:settings.base_schedule.monday.start',

            'base_schedule.tuesday'          => 'required|array',
            'base_schedule.tuesday.start'    => 'present',
            'base_schedule.tuesday.end'      => 'required_with:settings.base_schedule.tuesday.start',

            'base_schedule.wednesday'        => 'required|array',
            'base_schedule.wednesday.start'  => 'present',
            'base_schedule.wednesday.end'    => 'required_with:settings.base_schedule.wednesday.start',

            'base_schedule.thursday'          => 'required|array',
            'base_schedule.thursday.start'    => 'present',
            'base_schedule.thursday.end'      => 'required_with:settings.base_schedule.thursday.start',

            'base_schedule.friday'            => 'required|array',
            'base_schedule.friday.start'      => 'present',
            'base_schedule.friday.end'        => 'required_with:settings.base_schedule.friday.start',

            'base_schedule.saturday'          => 'required|array',
            'base_schedule.saturday.start'    => 'present',
            'base_schedule.saturday.end'      => 'required_with:settings.base_schedule.saturday.start',

            'base_schedule.sunday'             => 'required|array',
            'base_schedule.sunday.start'       => 'present',
            'base_schedule.sunday.end'         => 'required_with:settings.base_schedule.sunday.start',
        ];
    }
}
