<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Show all clients
    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    // Show create client form
    public function create()
    {
        return view('clients.form');
    }

    // Store new client
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'domain' => 'required|unique:clients,domain',
        ]);

        Client::create($request->all());
        return redirect()->route('clients.index');
    }

    // Show edit client form
    public function edit(Client $client)
    {
        return view('clients.form', compact('client'));
    }

    // Update client
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'domain' => 'required|unique:clients,domain,'.$client->id,
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index');
    }

    // Delete client
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index');
    }
}
