<?php

namespace App\Http\Controllers;

use App\Exceptions\EmployeeException;
use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends ApiController
{
    public function store(CreateEmployeeRequest $request, string $companyId): JsonResponse
    {
        if (! request()->hasValidSignature())
        {
            return $this->error('Invalid url signature.', 400);
        }

        // TODO: Check companies services.  When all employees are assigned to a particular service
        //       That is treated as 'all'.  Assign new employee to any service that is 'all'

        $employee = (new RegisterService)->employee($companyId);
            
        return $this->created(['employee' => $employee], 'Employee created.');
    }

    public function show(string $companyId, string $id)
    {
        return $this->ok(
            ['employee' => Employee::forCompany($companyId)->with('company')->findOrFail($id)], 'Employee account retrieved.'
        );
    }

    public function update(Request $request, string $companyId, string $id)
    {
        $employee = Employee::forCompany($companyId)->findOrFail($id);
        $this->authorize('update', $employee);

        DB::transaction(function () use ($employee)
        {
            $this->handlePasswordUpdate($employee);
        
            $employee->user->update(request()->only(['first_name', 'last_name', 'phone']));
        });

        return $this->ok($employee, 'Employee profile updated.');
    }

    public function delete(string $company_id, string $id)
    {
        $employee = Employee::findOrFail($id);

        if (! (auth()->user()->isOwner() && auth()->user()->employee->company_id === $employee->company_id) && 
            auth()->user()->id !== $id)
        {
            throw new EmployeeException([], 'User not authorized to perform this action');
        }

        DB::transaction(function () use ($employee)
        {
            $bookingIds = $employee->bookings()->pluck('id');

            TimeSlot::where('employee_id', $employee->id)->delete();
            Service::whereIn('booking_id', $bookingIds->all())->delete();
            Booking::whereIn('id', $bookingIds->all())->delete();
            $employee->delete();
            $employee->user->delete();
        });

        return $this->deleted('Employee deleted.');
    }

    private function handlePasswordUpdate(Employee $employee)
    {
        if (!! request('old_password'))
        {
            if (! request('new_password'))
            {
                throw new EmployeeException([], 'A new password was not provided.');
            }            

            $user = $employee->user;

            if (! Hash::check(request('old_password'), $user->password) )
            {
                throw new EmployeeException([], 'Old password was incorrect.');
            }            
            
            $user->password = Hash::make(request('new_password'));
            $user->save();
        }
    }
}
