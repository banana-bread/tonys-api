<?php

namespace App\Http\Controllers;

use App\Models\ServiceDefinition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceDefinitionController extends ApiController
{
    public function show(Request $request, string $id): JsonResponse
    {
        return $this->ok(
            ['service_definition' => ServiceDefinition::findOrFail($id), 'Service definition retrieved']
        );
    }

    public function index(): JsonResponse
    {
        $serviceDefinitions = ServiceDefinition::all(); 

        return $this->ok(['service_definitions' => $serviceDefinitions], 'Service definitions retrieved.');
    }
}