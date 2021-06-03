<?php

namespace App\Http\Controllers;

use App\Services\TimeSlot\TimeSlotService;
use App\Http\Requests\TimeSlotRequest;
use App\Models\Company;
use App\Models\Employee;

class EmployeeBaseScheduleController extends ApiController
{
    // TODO: create policy class
    // TODO: create form request class
    public function update(/* FormRequest */string $companyId, string $id)
    {
        /*
            - [ ] Make sure new schedule falls within company schedule.
            - [x] Gather any time slots that have been reserved and make copies.
            - [x] Get date of last time slot, determine number of days we need to make slots for.
            - [x] Delete remaining slots for today.
            - [x] Delete all future time slots.
            - [ ] Create future time slots with new base and end date.
            - [ ] From copies, mark new applicable slots as reserved.
        */


        // $company = Company::findOrFail($companyId);
        $employee = Employee::findOrFail($id);

        $baseSchedule = request('base_schedule');

        $employee->updateBaseSchedule($baseSchedule);
        








    }
}
