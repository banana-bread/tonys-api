<?php

namespace App\Services\TimeSlot;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TimeSlotException;

class TimeSlotPdo
{
    protected Carbon $dateFrom;
    protected Carbon $dateTo;
    protected $companyId;
    protected $employeeId;

    public function __construct(Carbon $dateFrom, Carbon $dateTo, string $companyId, $employeeId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->companyId = $companyId;
        $this->employeeId = $employeeId;
    }

    public function fetchAvailableSlots(): Collection
    {        
        $sql = $this->prepareAvailableSlotsSql();
        return $this->executeSqlAndProcessResults($sql);
    }

    public function fetchConsecutiveAvailableSlots(int $slotsRequired): Collection
    {
        $sql = $this->prepareConsecutiveAvailableSlotsSql($slotsRequired);
        return $this->executeSqlAndProcessResults($sql);
    }

    protected function executeSqlAndProcessResults(string $sql): Collection
    {
        $availableSlots = $this->executeAvailableSlotsSql($sql);

        if (! $this->employeeId)
        {
            $availableSlots = $this->chooseRandomSlotWhenManyAreAvailable($availableSlots);
        }

        return $this->mapAvailableSlotsCollection($availableSlots);
    }

    protected function prepareAvailableSlotsSql(): string
    {
        $andEmployeeIdPart = !!$this->employeeId
        ? "AND t.employee_id = :employee_id"
        : "";
    
        return    
            "SELECT t.id, t.company_id, t.employee_id, t.start_time, t.end_time, t.reserved
            FROM time_slots as t
            JOIN employees ON employees.id = t.employee_id
            WHERE date(t.start_time) >= date(:date_from)
            AND date(t.end_time) <= date(:date_to)
            AND t.start_time >= DATE_ADD(now(), INTERVAL 15 MINUTE)
            AND employees.bookings_enabled = 1
            AND t.company_id = :company_id " .
            $andEmployeeIdPart .
            " AND t.reserved = 0
             ORDER BY t.start_time";
    }

    protected function prepareConsecutiveAvailableSlotsSql(int $slotsRequired): string
    {
        $leadColumnsPart = "";
        $andLeadColumnsPart = "";

        for ($i = 1; $i < $slotsRequired; $i++)
        {
            $leadColumnsPart .= 
                "LEAD (reserved, $i) over(PARTITION BY employee_id ORDER BY start_time) AS next_reserved_$i,
                 LEAD (start_time, $i) over(PARTITION BY employee_id ORDER BY start_time) AS next_start_time_$i";
            
            $andLeadColumnsPart .= 
                "AND next_reserved_$i = 0
                 AND DATE(start_time) = DATE(next_start_time_$i)";

            if ($i != ($slotsRequired - 1))
            {
                $leadColumnsPart .= ",\n";
                $andLeadColumnsPart .= "\n";
            }
        }

        $andEmployeeIdPart = !!$this->employeeId
            ? "AND employee_id = :employee_id"
            : "";

        // This is currently hard-coded to 15 minutes but will need to be changed when adding more companies
        return
            "WITH s AS (SELECT id, company_id, employee_id, start_time, end_time, reserved, $leadColumnsPart
                        FROM time_slots
                        WHERE date(start_time) >= date(:date_from)
                        AND date(start_time) <= date(:date_to) 
                        AND start_time >= DATE_ADD(now(), INTERVAL 15 MINUTE)
                        AND company_id = :company_id                  
                        $andEmployeeIdPart)
                        SELECT s.id, s.company_id, s.employee_id, s.start_time, s.end_time
                        FROM s
                        JOIN employees on s.employee_id = employees.id
                        WHERE s.reserved = 0
                        AND employees.bookings_enabled = 1
                        $andLeadColumnsPart                        
                        ORDER BY start_time;";
    }

    protected function executeAvailableSlotsSql(string $sql): Collection
    {
        $stmt = DB::getPdo()->prepare($sql);
        $params = [
            ':date_from' => $this->dateFrom->copy()->startOfDay()->toDateString(),
            ':date_to' => $this->dateTo->copy()->endOfDay()->toDateString(),
            ':company_id' => $this->companyId,
        ];

        if (!! $this->employeeId)
        {
            $params[':employee_id'] = $this->employeeId;
        }

        // $queryWasSuccessful = $stmt->execute();
        $queryWasSuccessful = $stmt->execute($params);

        if (! $queryWasSuccessful)
        {
            throw new TimeSlotException([], 'There was an error in retrieving available time slots.');
        } 
        
        return collect($stmt->fetchAll());
    }

    protected function mapAvailableSlotsCollection(Collection $availableSlots): Collection
    {
        return $availableSlots->map(function ($slot) {
            return [
                'id' => $slot['id'],
                'company_id' => $slot['company_id'],
                'employee_id' => $slot['employee_id'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
            ];
        });
    }

    protected function chooseRandomSlotWhenManyAreAvailable(Collection $availableSlots): Collection
    {
        $availableSlots = $availableSlots->groupBy('start_time');

        return $availableSlots->map(function($slot) {
            return $slot->random();
        })->values();
    }
}
