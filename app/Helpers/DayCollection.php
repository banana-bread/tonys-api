<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DayCollection extends Collection
{
    // HELPERS
    public static function fromRange(Carbon $start, Carbon $end)
    {
        return new DayCollection(self::_createDaysFromRange($start, $end));
    }

    public function toStartOfDay()
    {
        $this->each(function ($day) {
            $day->startOfDay();
        });
    }

    // PRIVATE HELPERS

    private static function _createDaysFromRange(Carbon $start, Carbon $end): array
    {
        $numberOfDays = $start->diffInDays($end);
        $days = [];

        for ($i = 0; $i < $numberOfDays; $i++)
        {
            $days[] = $start->copy()->addDays($i);
        }

        return $days;
    }
}
