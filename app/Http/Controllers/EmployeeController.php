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

class EmployeeController extends ApiController
{
    public function store(CreateEmployeeRequest $request, string $companyId): JsonResponse
    {
        if (! request()->hasValidSignature())
        {
            return $this->error('Invalid url signature.', 400);
        }

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

        $employee->user->update(request()->only(['first_name', 'last_name', 'phone']));

        return $this->ok($employee, 'Employee profile updated.');
    }

    public function delete(string $company_id, string $id)
    {
        $employee = Employee::findOrFail($id);

        if (! (auth()->user()->isOwner() && auth()->user()->employee->company_id === $employee->company_id) && 
            auth()->user()->id !== $id)
        {
            throw new EmployeeException([], 'User unauthorized to perform this action');
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


}
