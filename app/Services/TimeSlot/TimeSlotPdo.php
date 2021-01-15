<?php

namespace App\Services\TimeSlot;

use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TimeSlotException;

class TimeSlotPdo
{
    protected Carbon $dateFrom;
    protected Carbon $dateTo;
    protected $employeeId;

    public function __construct(Carbon $dateFrom, Carbon $dateTo, $employeeId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->employeeId = $employeeId;        
    }

    public function fetchAvailableSlots(): Collection
    {
        $query = TimeSlot::where('reserved', 0)
                         ->where('start_time', '>', $this->start_time) 
                         ->where('end_time', '<', $this->end_time); 
        
        if (!! $this->employeeId)
        {
            $query = $query->where('employee_id', $this->employeeId);
        }

        return $query->get();
    }

    public function fetchConsecutiveAvailableSlots(int $slotsRequired): Collection
    {
        $leadColumnsPart = "";
        $whereLeadColumnsPart = "";

        for ($i = 1; $i < $slotsRequired; $i++)
        {
            $leadColumnsPart .= 
                "LEAD (reserved, $i) over(PARTITION BY employee_id ORDER BY start_time) AS next_reserved_$i,
                 LEAD (start_time, $i) over(PARTITION BY employee_id ORDER BY start_time) AS next_start_time_$i";
            
            $whereLeadColumnsPart .= 
                "AND next_reserved_$i = 0
                 AND DATE(start_time) = DATE(next_start_time_$i)";

            if ($i != ($slotsRequired - 1))
            {
                $leadColumnsPart .= ",\n";
                $whereLeadColumnsPart .= "\n";
            }
        }

        $whereEmployeeIdPart = !!$this->employeeId
            ? "WHERE employee_id = :employee_id"
            : "";

        $sql = 
            "WITH s AS (SELECT id, employee_id, start_time, end_time, reserved, $leadColumnsPart
                        FROM time_slots
                        $whereEmployeeIdPart)
                        SELECT id, employee_id, start_time, end_time
                        FROM s
                        WHERE reserved = 0
                        $whereLeadColumnsPart;";

        
        $stmt = DB::getPdo()->prepare($sql);
        $params = [':employee_id' => $this->employeeId];

        $queryWasSuccessful = !!$this->employeeId
            ? $stmt->execute($params)
            : $stmt->execute();

        if (! $queryWasSuccessful)
        {
            throw new TimeSlotException([], 'There was an error in retrieving available time slots.');
        } 

        $availableSlots = $stmt->fetchAll();

        return collect($availableSlots)->map(function ($slot) {
            return [
                'id' => $slot['id'],
                'employee_id' => $slot['employee_id'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
            ];
        });
    }
}
