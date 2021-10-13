<?php

namespace App\Traits;

use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait CreatesTimeSlots
{
    public function createSlotsForNext(int $numberOfDays): Collection
    {
        $singleSlotDuration = $this->company->time_slot_duration;
        $secondsIn24Hours = 86400;
        $numberOfSlots = ($secondsIn24Hours / $singleSlotDuration) * $numberOfDays;
        $slots = new Collection();

        $startDate = $this->hasFutureSlots()
            ? $this->latest_time_slot->start_time->copy()->startOfDay()->addDay()
            : today();
        
        for ($i = 0; $i < $numberOfSlots; $i++)
        {
            $startTime = $startDate->copy()->addSeconds($i * $singleSlotDuration);
            $endTime = $startTime->copy()->addSeconds($singleSlotDuration);

            $slots->push( $this->_makeSlot($startTime, $endTime) );
        }

        $this->_insertSlots($slots);
        return $slots;
    }

    private function _makeSlot(Carbon $start, Carbon $end): array
    {
        return [
            'employee_id' => $this->id,
            'company_id' => $this->company_id,
            'reserved' => false,
            'employee_working' => $this->_isWorking($start, $end),
            'start_time' => $start,
            'end_time' => $end,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function _isWorking(Carbon $startTime, Carbon $endTime): bool
    {
        if ((!$startTime || !$endTime) || !$startTime->isSameDay($endTime)) return false;


        $isStartBeforeOrEqualToBaseScheduleStart = ($startTime->copy()->unix() - $startTime->copy()->startOfday()->unix()) >= $this->base_schedule->start($startTime);
        $isEndBeforeOrEqualToBaseScheduleEnd = ($endTime->copy()->unix() - $endTime->copy()->startOfDay()->unix() <= $this->base_schedule->end($endTime));

        return $isStartBeforeOrEqualToBaseScheduleStart && $isEndBeforeOrEqualToBaseScheduleEnd;
    }

    private function _insertSlots(Collection $slots)
    {
        $slots->chunk(5000)
            ->each(fn ($slotChunk) => TimeSlot::insert($slotChunk->all()));;
    }
}
