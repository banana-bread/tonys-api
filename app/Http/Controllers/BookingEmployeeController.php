<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class BookingEmployeeController extends ApiController
{
    public function index(string $companyId): JsonResponse
    {
        $employees = 
            Employee::forCompany($companyId)
                ->where('bookings_enabled', true)
                ->get();

        return $this->ok(
            ['employees' => $employees], 'Employees retrieved.'
        );
    }
}
