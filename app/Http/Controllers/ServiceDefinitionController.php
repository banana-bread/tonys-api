<?php

namespace App\Http\Controllers;

use App\Models\ServiceDefinition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceDefinitionController extends ApiController
{
    public function index(): JsonResponse
    {
        $serviceDefinitions = ServiceDefinition::all(); 

        return $this->ok(['service_definitions' => $serviceDefinitions], 'Service definitions retrieved.');
    }
}