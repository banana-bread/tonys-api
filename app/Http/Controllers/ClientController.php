<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\ClientService;
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
        $client = $service->create($request->all());

        return $this->success($client, 'Client created.', 201);
    }

    public function show($id)
    {
        $service = new ClientService();
        $client = $service->get($id);

        return $this->success($client, 'Client retreived.');
    }

    public function update(ClientRequest $request, string $id)
    {
        // TODO: implement
        // $service = new ClientService();
        // $client = $service->create($request);

        // return $this->success($client, 'Client created.', 201);
    }

    public function destroy($id)
    {
        //
    }
}
