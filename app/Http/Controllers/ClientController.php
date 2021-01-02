<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class ClientController extends ApiController
{
    public function index()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $service = new AuthService();
        $client = $service->registerClient($request);

        return $this->success($client, 'Client registered.', 201);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
