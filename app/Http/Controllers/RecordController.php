<?php

namespace App\Http\Controllers;

Use App\Models\Record;
Use App\Http\Resources\RecordResource;
use App\Models\Process;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index(Request $request, Process $process)
    {
        return RecordResource::collection(Record
            ::belongsToUser($request->user())
            ->belongsToProcess($process)
            ->latest()
            ->paginate());
    }

    public function search(Request $request, Process $process, $reference)
    {
        return RecordResource::collection(Record
            ::belongsToUser($request->user())
            ->belongsToProcess($process)
            ::where('reference', 'like', '%'.$reference.'%')
            ->get());
    }

    public function show(Record $record)
    {
        return new RecordResource($record);
    }

    public function store(Request $request, Process $process) {
        
        $request->validate([
            'run' => 'required|max:255',
            'type' => 'required|max:255',
            'reference' => 'max:255',
            'status' => 'required|exists:process_statuses,name,process_id,' . $process->id,
            'value' => 'json',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:255',
        ]);
                
        // Change plain status name into ProcessStatus
        $statusName = $request->status;
        $processStatus = $process->processStatuses->firstWhere('name', $statusName);

        $user = $request->user();

        // Add record
        $record = new Record;
        $record->user_id = $user->id;
        $record->client_id = $user->client->id;
        $record->process_id = $process->id;
        $record->run = $request->run;
        $record->type = $request->type;
        $record->reference = $request->reference;
        $record->process_status_id = $processStatus->id;
        $record->save();

        if ($request->value) {
            $record->addValue($request->value);
        }

        $record->updateTags($request->tags);
        
        return new RecordResource($record);
    }

    public function update(Request $request, Record $record) 
    {        
        $request->validate([            
            'run' => 'optional',
            'type' => 'optional',
            'reference' => 'optional',
            'status' => 'optional',
            'value' => 'optional'
        ]);

        $record->update($request->all());

        return new RecordResource($record);
    }

    public function destroy(Process $process, Record $record)
    {
        $record->delete();

        return response()->json(['message' => 'Success.']);
    }
}
