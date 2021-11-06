<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyServiceDefinitionsController extends ApiController
{
    public function update(string $companyId): JsonResponse
    {
        $attributes = collect( request()->all() );

        Company::findOrFail($companyId)->service_definitions()->each(
            fn($service) => $service->update( $attributes->firstWhere('id', $service->id) ) 
        );
        
        return $this->ok(
            ['company' => Company::where('id', $companyId)->with('employees')->get()], 'Employees updated.'
        );
    } 
}
