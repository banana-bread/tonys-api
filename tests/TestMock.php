<?php

namespace Tests;

class TestMock 
{
    public static function employee_settings(): array
    {
        return [
            'base_schedule' => [
                'monday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'tuesday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'wednesday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'thursday' => [ 
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'friday' => [
                    'start' => '09:00',
                    'end' => '17:00',
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

    public static function employee_settings_no_days_off(): array
    {
        return [
            'base_schedule' => [
                'monday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'tuesday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'wednesday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'thursday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'friday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'saturday' => [
                    'start' => '09:00',
                    'end' => '17:00',
                ],
                'sunday' => [
                    'start' => '09:00',
                    'end' => '17:00',
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
