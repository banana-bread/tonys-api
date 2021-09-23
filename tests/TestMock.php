<?php

namespace Tests;

use Faker\Factory;

/*

    All times here are 1pm - 9pm and are stored as UTC.  Will be 9am - 5pm EST 

*/
class TestMock 
{
    public static function employee_settings(): array
    {
        $start = today()->addHours(13)->timestamp - today()->timestamp;
        $end = today()->addHours(21)->timestamp - today()->timestamp;

        return [
            'base_schedule' => [
                'monday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'tuesday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'wednesday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'thursday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'friday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'saturday' => [
                    'start' => null,
                    'end' => null,
                ],
                'sunday' => [
                    'start' => null,
                    'end' => null,
                ],
            ]            
        ];
    }

    public static function employee_settings_base_schedule_ends_on_quarter_hour(): array
    {
        $start = today()->addHours(13)->timestamp - today()->timestamp;
        $end = today()->addHours(21)->addMinutes(15)->timestamp - today()->timestamp;

        return [
            'base_schedule' => [
                'monday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'tuesday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'wednesday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'thursday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'friday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'saturday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'sunday' => [
                    'start' => $start,
                    'end' => $end,
                ],
            ]            
        ];
    }

    public static function employee_settings_no_days_off(): array
    {
        $start = today()->addHours(13)->timestamp - today()->timestamp;
        $end = today()->addHours(21)->timestamp - today()->timestamp;

        return [
            'base_schedule' => [
                'monday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'tuesday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'wednesday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'thursday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'friday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'saturday' => [
                    'start' => $start,
                    'end' => $end,
                ],
                'sunday' => [
                    'start' => $start,
                    'end' => $end,
                ],
            ]            
        ];
    }

    public static function employee_settings_no_working_days(): array
    {
        return [
            'base_schedule' => [
                'monday' => [
                    'start' => null,
                    'end' => null,
                ],
                'tuesday' => [
                    'start' => null,
                    'end' => null,
                ],
                'wednesday' => [
                    'start' => null,
                    'end' => null,
                ],
                'thursday' => [
                    'start' => null,
                    'end' => null,
                ],
                'friday' => [
                    'start' => null,
                    'end' => null,
                ],
                'saturday' => [
                    'start' => null,
                    'end' => null,
                ],
                'sunday' => [
                    'start' => null,
                    'end' => null,
                ],
            ]            
        ];
    }

    // public static function company_settings(): array
    // {
    //     $start = today()->addHours(13)->timestamp - today()->timestamp;
    //     $end = today()->addHours(21)->timestamp - today()->timestamp;

    //     return [
    //         'base_schedule' => [
    //             'monday' => [
    //                 'start' => $start,
    //                 'end' => $end,
    //             ],
    //             'tuesday' => [
    //                 'start' => $start,
    //                 'end' => $end,
    //             ],
    //             'wednesday' => [
    //                 'start' => $start,
    //                 'end' => $end,
    //             ],
    //             'thursday' => [
    //                 'start' => $start,
    //                 'end' => $end,
    //             ],
    //             'friday' => [
    //                 'start' => $start,
    //                 'end' => $end,
    //             ],
    //             'saturday' => [
    //                 'start' => null,
    //                 'end' => null,
    //             ],
    //             'sunday' => [
    //                 'start' => null,
    //                 'end' => null,
    //             ],
    //         ]            
    //     ];
    // }
}
