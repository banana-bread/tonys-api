<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Models\Client;
use App\Services\Auth\RegisterService;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;


class ClientController extends ApiController
{
    public function index()
    {
        //
    }

    public function store(CreateClientRequest $request): JsonResponse
    {
        $service = new RegisterService();
        $client = $service->client($request->all());

        return $this->created(['client' => $client], 'Client account created.');
    }

    public function show(string $id): JsonResponse
    {
        $client = Client::findOrFail($id);

        return $this->ok(['client' => $client], 'Client retrieved.');
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
