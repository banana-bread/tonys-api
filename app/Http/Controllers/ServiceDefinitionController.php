<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceDefinitionRequest;
use App\Models\Company;
use App\Models\ServiceDefinition;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ServiceDefinitionController extends ApiController
{   
    public function store(CreateServiceDefinitionRequest $request, string $companyId)
    {
        $this->authorize('create', ServiceDefinition::class);
        
        $ordinalPosition = Company::where('companies.id', $companyId)
            ->join('service_definitions', 'service_definitions.company_id', '=', 'companies.id')
            ->count();
        
        $attributes = array_merge(
            request()->only('name', 'price', 'duration'), 
            ['company_id' => $companyId, 'ordinal_position' => $ordinalPosition]
        );

        $service = ServiceDefinition::create($attributes);

        $service->employees()->sync(request('employee_ids'));

        return $this->created(
            ['service_definition' => $service, 'Service definition created']
        );
    }

    public function show(string $companyId, string $id): JsonResponse
    {
        return $this->ok(
            ['service_definition' => ServiceDefinition::forCompany($companyId)->findOrFail($id), 'Service definition retrieved']
        );
    }

    public function update(CreateServiceDefinitionRequest $request, string $companyId, string $id): JsonResponse
    {
        $service = ServiceDefinition::forCompany($companyId)->findOrFail($id);
        $this->authorize('update', $service);

        $service->employees()->sync(request('employee_ids'));

        return $this->ok([
            'service_definition' => $service->update(request()->only(['id', 'name', 'price', 'duration'])), 
            'Service definition updated.'
        ]);
    }

    public function index(string $companyId): JsonResponse
    {
        $services = ServiceDefinition::where('company_id', $companyId)->get();

        return $this->ok(
            ['service_definitions' => $services, 'Service definitions retrieved.']
        );
    }

    public function destroy(string $companyId, string $id): JsonResponse
    {
        $service = ServiceDefinition::forCompany($companyId)->findOrFail($id);
        $this->authorize('delete', $service);

        DB::table('employee_service_definition')
            ->where('service_definition_id', $service->id)
            ->delete();
            
        $service->delete();

        return $this->deleted('Service definition deleted');
    }
}