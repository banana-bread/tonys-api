<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceDefinitionRequest;
use App\Models\ServiceDefinition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceDefinitionController extends ApiController
{
    // TODO: reimplemet the form requests once management app auth is done.
    
    public function store(CreateServiceDefinitionRequest $request, string $companyId)
    {
        $this->authorize('create', ServiceDefinition::class);

        return $this->created(
            ['service_definition' => ServiceDefinition::create(array_merge($request->all(), ['company_id' => $companyId])), 'Service definition created']
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

        return $this->ok(
            ['service_definition' => $service->update($request->all()), 'Service definition updated.']
        );
    }

    public function index(string $companyId): JsonResponse
    {
        return $this->ok(
            ['service_definitions' => ServiceDefinition::forCompany($companyId)->get(), 'Service definitions retrieved.']
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