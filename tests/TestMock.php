<?php

namespace Tests;

use Faker\Factory;

class TestMock 
{
    public static function employee_settings(): array
    {
        $start = today()->addHours(9)->timestamp - today()->timestamp;
        $end = today()->addHours(17)->timestamp - today()->timestamp;

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
}
