<?php

namespace App\Http\Controllers;

use App\Helpers\BaseSchedule;
use App\Http\Requests\UpdateCompanyBaseScheduleRequest;
use App\Models\Company;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class CompanyBaseScheduleController extends ApiController
{
    public function update(UpdateCompanyBaseScheduleRequest $request, string $companyId)
    {
        $company = Company::findOrFail($id);
        
        if (! auth()->user()->isAdmin() || 
              auth()->user()->employee->company_id != $company->id)
        {
            throw new AuthorizationException('User not authorized');
        }
        
        $employee->updateBaseSchedule(new BaseSchedule(request('base_schedule')));

        return $this->ok(['employee' => $employee], 'Employee base schedule updated');
    }
}
