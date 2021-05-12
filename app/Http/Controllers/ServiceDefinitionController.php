<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceDefinitionRequest;
use App\Models\ServiceDefinition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceDefinitionController extends ApiController
{
    // TODO: reimplemet the form requests once management app auth is done.
    
    public function store(CreateServiceDefinitionRequest $request)
    // public function store(Request $request)
    {
        $this->authorize('create', ServiceDefinition::class);

        return $this->created(
            ['service_definition' => ServiceDefinition::create($request->all()), 'Service definition created']
        );
    }

    public function show(Request $request, string $id): JsonResponse
    {
        return $this->ok(
            ['service_definition' => ServiceDefinition::findOrFail($id), 'Service definition retrieved']
        );
    }

    // public function update(Request $request, string $id): JsonResponse
    public function update(CreateServiceDefinitionRequest $request, string $id): JsonResponse
    {
        $service = ServiceDefinition::findOrFail($id);
        $this->authorize('update', $service);

        return $this->ok(
            ['service_definition' => $service->update($request->all()), 'Service definition updated.']
        );
    }

    public function index(): JsonResponse
    {
        return $this->ok(
            ['service_definitions' => ServiceDefinition::all()], 'Service definitions retrieved.'
        );
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $service = ServiceDefinition::findOrFail($id);
        $this->authorize('delete', $service);

        $service->delete();
        return $this->deleted('Service definition deleted');
    }
}