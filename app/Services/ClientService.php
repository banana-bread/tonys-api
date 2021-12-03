<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function create(array $attributes): Client
    {   
        return DB::transaction(function () use ($attributes) {
            $user = User::create($attributes);
            
            return $user->client()->create();
        });
    }

    public function update(array $attributes, string $id): Client
    {
        return DB::transaction(function () use ($attributes, $id) {
            $client = Client::findOrFail($id);
            $client->fill($attributes);
            $client->save();
            
            return $client;
        });
    }
}
