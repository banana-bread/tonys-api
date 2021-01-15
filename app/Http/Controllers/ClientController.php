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

    public function store(ClientRequest $request): JsonResponse
    {
        $service = new ClientService();
        $client = $service->create($request);

        return $this->success($client, 'Client created.', 201);
    }

    public function show($id)
    {
  
    }

    public function update(ClientRequest $request, $id)
    {
        $service = new ClientService();
        $client = $service->create($request);

        return $this->success($client, 'Client created.', 201);
    }

    public function destroy($id)
    {
        //
    }
}
