<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceDefinitionRequest;
use App\Http\Requests\DeleteServiceDefinitionRequest;
use App\Models\ServiceDefinition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceDefinitionController extends ApiController
{
    // TODO: reimplemet the form requests once management app auth is done.
    
    // public function store(CreateServiceDefinitionRequest $request)
    public function store(Request $request)
    {
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

    // public function update(CreateServiceDefinitionRequest $request, string $id): JsonResponse
    public function update(Request $request, string $id): JsonResponse
    {
        return $this->ok(
            ['service_definition' => ServiceDefinition::findOrFail($id)->update($request->all()), 'Service definition updated.']
        );
    }

    public function index(): JsonResponse
    {
        return $this->ok(
            ['service_definitions' => ServiceDefinition::all()], 'Service definitions retrieved.'
        );
    }

    // public function destroy(DeleteServiceDefinitionRequest $request, string $id): JsonResponse
    public function destroy(Request $request, string $id): JsonResponse
    {
        ServiceDefinition::findOrFail($id)->delete();
        return $this->deleted('Service definition deleted');
    }
}