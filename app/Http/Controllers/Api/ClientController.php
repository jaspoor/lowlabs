<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProcessResource;
use App\Models\Client;
use App\Models\Process;
use Illuminate\Http\Request;

class ClientController extends BaseController
{
    public function index() 
    {
        return ClientResource::collection(Client::latest()->paginate());
    }

    public function show(Client $client)
    {
        return new ClientResource($client);
    }

    public function processes(Client $client)
    {
        return ProcessResource::collection(Process::
            belongsToClient($client)
            ->paginate()
        );
    }

    public function store(Request $request)
    {        
        $request->validate([
            'name' => 'required|max:255'
        ]);

        $client = Client::create($request->all());

        return new ClientResource($client);
    }

    public function update(Request $request, Client $client) {
        
        $request->validate([
            'name' => 'required'
        ]);

        $client->update($request->all());

        return new ClientResource($client);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(['message' => 'Success.']);
    }
}
