<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceDefinitionRequest;
use App\Models\Company;
use App\Models\ServiceDefinition;
use Illuminate\Http\JsonResponse;

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

        return $this->ok([
            'service_definition' => $service->update(request()->only(['id', 'name', 'price', 'duration'])), 
            'Service definition updated.'
            ]);
    }

    public function index(string $companyId): JsonResponse
    {
        return $this->ok(
            ['service_definitions' => Company::findOrFail($companyId)->service_definitions, 'Service definitions retrieved.']
        );
    }

    public function destroy(string $companyId, string $id): JsonResponse
    {
        $service = ServiceDefinition::forCompany($companyId)->findOrFail($id);
        $this->authorize('delete', $service);

        $service->delete();
        return $this->deleted('Service definition deleted');
    }
}