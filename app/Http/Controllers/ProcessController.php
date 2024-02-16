<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProcessResource;
use App\Models\Process;
use App\Models\ProcessStatus;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index(Request $request)
    {
        return ProcessResource::collection(Process
            ::belongsToClient($request->user()->client)
            ->latest()
            ->paginate());
    }

    public function show(Process $process)
    {
        return new ProcessResource($process);
    }

    public function store(Request $request)
    {        
        $request->validate([
            'name' => 'required|max:255',
            'statuses' => 'required|array',
            'statuses.*' => 'required|string|max:255',
        ]);

        $process = new Process(['name' => $request->name]);
        $process->client_id = $request->user()->client->id;
        $process->save();

        $process->updateStatuses($request->statuses);

        return new ProcessResource($process);
    }

    public function update(Request $request, Process $process) {
        
        $request->validate([
            'name' => 'required|max:255',
            'statuses' => 'array',
            'statuses.*' => 'string|max:255',
        ]);

        $process->update(['name' => $request->name]);

        if ($request->has('statuses')) {
            $process->updateStatuses($request->statuses);
        }
        
        return new ProcessResource($process);
    }

    public function destroy(Process $process)
    {
        $process->delete();

        return response()->json(['message' => 'Success.']);
    }
}
