<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyRequest;
use App\Models\Company;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class CompanyController extends ApiController
{
    public function index()
    {
        // TODO: figure out pagination
    }

    public function store(CreateCompanyRequest $request): JsonResponse
    {
        $register = new RegisterService();
        $company = $register->company();

        return $this->created(['company' => $company], 'Company created.');
    }

    public function show(string $id): JsonResponse
    {
        $company = Company::where('id', $id)->with('employees')->first();

        return $this->ok(['company' => $company], 'Company retrieved.');
    }

    // TODO: this will need to be a cascading delete or soft delete?
    // public function destroy(string $id): JsonResponse
    // {
    //     $service = new BookingService();
    //     $service->cancel($id);

    //     return $this->deleted('Booking cancelled.');
    // }
}
