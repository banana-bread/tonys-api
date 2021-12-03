<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\JsonResponse;

class BookingEmployeeController extends ApiController
{
    public function index(string $companyId): JsonResponse
    {
        $employees = Company::findOrFail($companyId)
            ->employees()
            ->where('bookings_enabled', true)
            ->get();

        return $this->ok(
            ['employees' => $employees], 'Employees retrieved.'
        );
    }
}
