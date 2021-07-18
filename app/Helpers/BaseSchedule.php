<?php 

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BaseSchedule
{
    protected Collection $base_schedule;

    public function __construct(array $baseSchedule)
    {    
        $this->base_schedule = collect($baseSchedule);    
    }

    /**
     * Check if this BaseSchedule falls within a provided BaseSchedule. 
     *
     * @param  BaseSchedule  $baseSchedule
     * @return bool
     */
    public function fallsWithin(BaseSchedule $baseSchedule): bool
    {
        return $this->base_schedule->contains(function ($schedule, $day) use ($baseSchedule) {
            return $this->start($day) >= $baseSchedule->start($day) &&
                   $this->end($day) <= $baseSchedule->end($day);
        });
    }

    /**
     * Check if two base schedules are exactly the same.
     * 
     * @param BaseSchedule $baseSchedule
     * @return bool
     */
    public function matches(BaseSchedule $baseSchedule): bool
    {
        return !$this->base_schedule->contains(function ($schedule, $day) use ($baseSchedule) {
            return $this->start($day) !== $baseSchedule->start($day) ||
                   $this->end($day) !== $baseSchedule->end($day);
        });
    }

    /**
     * Get base schedule start time for a provided day. Today start if no day provided.
     * 
     * @param string $dayOfWeek
     * @return int
     */
    public function start(string $dayOfWeek = null): ?int
    {
        return $this->getBaseTimeForDay('start', $dayOfWeek);
    }

    /**
     * Get base schedule end time for a provided day. Today end if no day provided.
     * 
     * @param string $dayOfWeek
     * @return int
     */
    public function end(string $dayOfWeek = null): ?int
    {
        return $this->getBaseTimeForDay('end', $dayOfWeek);
    }

    public function toArray(): array
    {
        return $this->base_schedule->toArray();
    }

    protected function getBaseTimeForDay(string $timeOfDay, $dayOfWeek): ?int
    {
        return $dayOfWeek
            ? $this->getBaseTimeOrNull($dayOfWeek, $timeOfDay)
            : $this->getBaseTimeOrNull(today()->englishDayOfWeek, $timeOfDay);
    }

    protected function getBaseTimeOrNull(string $dayOfWeek, string $timeOfDay): ?int
    {
        if (isset($this->base_schedule->get(Str::lower($dayOfWeek))[$timeOfDay]))
        { 
            return $this->base_schedule->get(Str::lower($dayOfWeek))[$timeOfDay];
        }

        return null;
    }
}