<?php

namespace App\Traits;

use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait CreatesTimeSlots
{
    public function createSlotsForNext(int $numberOfDays): Collection
    {
        $companyData = $this->company()->select('time_slot_duration', 'timezone')->first();
        $singleSlotDuration = $companyData->time_slot_duration;
        $localTimeZone = $companyData->timezone;
        $secondsIn24Hours = 86400;
        $numberOfSlots = ($secondsIn24Hours / $singleSlotDuration) * $numberOfDays;
        $slots = new Collection();

        $startDate = $this->hasFutureSlots()
            ? $this->latest_time_slot->end_time->copy()
            : today();

            
        collect(range(0, $numberOfSlots))
            ->each(function ($i) use ($startDate, $singleSlotDuration, $slots, $localTimeZone)
            {
                $startTime = $startDate->copy()->addSeconds($i * $singleSlotDuration);
                $endTime = $startTime->copy()->addSeconds($singleSlotDuration);

                $slots->push( $this->_makeSlot($startTime, $endTime, $localTimeZone) );
            });

        $this->_insertSlots($slots);
        return $slots;
    }

    private function _makeSlot(Carbon $start, Carbon $end, string $localTimeZone): array
    {
        return [
            'employee_id' => $this->id,
            'company_id' => $this->company_id,
            'reserved' => false,
            'employee_working' => $this->_isWorking($start, $end, $localTimeZone),
            'start_time' => $start,
            'end_time' => $end,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function _isWorking(Carbon $slotStartTime, Carbon $slotEndTime, string $localTimezone): bool
    {
        if (! $slotStartTime->isSameDay($slotEndTime)) return false;

        $baseScheduleStart = $this->base_schedule->start($slotStartTime);
        $baseScheduleEnd = $this->base_schedule->end($slotEndTime);

        if (! $baseScheduleStart || ! $baseScheduleEnd) return false;

        $localStartTimeString = $slotStartTime->copy()->format('Y-m-d') . ' ' . $baseScheduleStart;
        $localEndTimeString = $slotEndTime->copy()->format('Y-m-d') . ' ' . $baseScheduleEnd;

        $currentDateStartTime = Carbon::parse($localStartTimeString, $localTimezone)->setTimezone('UTC');
        $currentDateEndTime = Carbon::parse($localEndTimeString, $localTimezone)->setTimezone('UTC');

        return $slotStartTime->gte($currentDateStartTime) && $slotEndTime->lte($currentDateEndTime);
    }

    private function _insertSlots(Collection $slots)
    {
        $slots->chunk(5000)
            ->each(fn ($slotChunk) => TimeSlot::insert($slotChunk->all()));
    }
}
