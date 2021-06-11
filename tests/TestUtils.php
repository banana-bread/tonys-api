<?php

namespace Tests;

use App\Helpers\BaseSchedule;

use ReflectionMethod;

class TestUtils 
{
    public static function callMethod($object, string $method, array $parameters = [])
    {
        $className = get_class($object);
        $reflection = new ReflectionMethod($className, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $parameters);
    }

    public static function mockBaseSchedule(int $startHour, int $endHour)
    {
        $start = today()->addHours($startHour)->timestamp - today()->timestamp;
        $end = today()->addHours($endHour)->timestamp - today()->timestamp;

        return new BaseSchedule([
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
        ]);
    }
}
