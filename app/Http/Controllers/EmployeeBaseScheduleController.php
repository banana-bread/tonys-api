<?php

namespace App\Http\Controllers;

use App\Services\TimeSlot\TimeSlotService;
use App\Http\Requests\TimeSlotRequest;
use App\Models\Company;

class EmployeeBaseScheduleController extends ApiController
{
    // TODO: create policy class
    // TODO: create form request class
    public function update(/* FormRequest */string $companyId, string $id)
    {
        /*
            - [ ] Make sure new schedule falls within company schedule.
            - [ ] Gather any time slots that have been reserved and make copies.
            - [ ] Get date of last time slot, determine number of days we need to make slots for.
            - [ ] Delete all future time slots, starting from next day.
            - [ ] Create future time slots with new base and end date.
            - [ ] From copies, mark new applicable slots as reserved.
        */

        
        Company::findOrFail($companyId);





    }
}
