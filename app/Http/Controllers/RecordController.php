<?php

namespace App\Http\Controllers;

Use App\Models\Record;
Use App\Http\Resources\RecordResource;
use App\Models\Process;
use App\Models\Tag;
use Illuminate\Http\Request;
use Doctrine\DBAL\Query\QueryBuilder;

class RecordController extends Controller
{
    public function index(Request $request, Process $process)
    {
        $tagNames = Tag::pluck('name')->toArray();
        $tagParams = array_fill_keys($tagNames, 'max:255');

        $validatedData = $request->validate([
            'reference' => 'max:255',
            'status' => 'max:255',
        ] + $tagParams);

        
        $query = Record
            ::query()
            ->belongsToUser($request->user())
            ->belongsToProcess($process);

        // Find by status
        if (isset($validatedData['reference'])) {
            $query->where('reference', $validatedData['reference']);
        }

        // Find by status
        if (isset($validatedData['status'])) {
            $query->whereHas('processStatus', function($query) use ($validatedData) {
                $query->where('name', $validatedData['status']);
            });
        }

        // Find by tag(s)
        foreach ($tagNames as $tagName) {
            if (isset($validatedData[$tagName])) {
                $query->whereHas('tagValues.tag', function ($query) use ($tagName, $validatedData) {
                    $query->where('name', $tagName)->where('value', $validatedData[$tagName]);
                });
            }
        }

        return RecordResource::collection($query->latest()->paginate());
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
