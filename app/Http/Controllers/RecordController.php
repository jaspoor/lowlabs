<?php

namespace App\Http\Controllers;

Use App\Models\Record;
Use App\Http\Resources\RecordResource;
use App\Models\Process;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index(Request $request, Process $process)
    {
        $tagNames = Tag::pluck('name')->toArray();
        $tagParams = array_fill_keys($tagNames, 'max:255');

        $sortableFields = ['run', 'type', 'reference', 'created_at'];

        $validatedData = $request->validate([
            'reference' => 'max:255',
            'status' => 'max:255',
            'sort' => 'nullable|in:' . implode(',', $sortableFields),
            'dir' => 'nullable|in:asc,desc', 
            'created_before' => 'nullable|date',
            'created_after' => 'nullable|date',
            'created_between' => 'nullable|date_range',       
        ] + $tagParams);
        
        $query = Record
            ::query()
            ->belongsToUser($request->user())
            ->belongsToProcess($process);

        // Find by status
        if (isset($validatedData['reference'])) {
            $query->whereIn('reference', explode(',', $validatedData['reference']));
        }

        // Find by status
        if (isset($validatedData['status'])) {
            $statusArray = explode(',', $validatedData['status']);            
            $query->whereHas('processStatus', function($query) use ($statusArray) {
                $query->where(function ($query) use ($statusArray) {
                    foreach ($statusArray as $status) {
                        $query->orWhere('name', $status);
                    }
                });
            });    
        }

        // Find by tag(s)
        foreach ($tagNames as $tagName) {
            if (isset($validatedData[$tagName])) {
                $tagValues = explode(',', $validatedData[$tagName]);
                $query->whereHas('tagValues.tag', function ($query) use ($tagName, $tagValues) {
                    $query->where('name', $tagName)->whereIn('value', $tagValues);
                });
            }
        }

        // Filter by created date
        if (isset($validatedData['created_before'])) {
            $createdBefore = Carbon::parse($validatedData['created_before']);
            $query->where('created_at', '<=', $createdBefore);
        }

        if (isset($validatedData['created_after'])) {
            $createdAfter = Carbon::parse($validatedData['created_after']);
            $query->where('created_at', '>=', $createdAfter);
        }

        if (isset($validatedData['created_between'])) {
            $dates = explode(',', $validatedData['created_between']);
            $startDate = Carbon::parse($dates[0]);
            $endDate = Carbon::parse($dates[1]);
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Add order
        if (isset($validatedData['sort'])) {
            $sort = $validatedData['sort'];
            $dir = $validatedData['dir'] ?? 'asc';
            
            $query->orderBy($sort, $dir);
        }
        
        return RecordResource::collection($query->latest()->paginate());
    }

    public function show(Process $process, Record $record)
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

    public function update(Request $request, Process $process, Record $record) {
        
        $request->validate([
            'run' => 'sometimes|required|max:255',
            'type' => 'sometimes|required|max:255',
            'reference' => 'sometimes|max:255',
            'status' => 'sometimes|required|exists:process_statuses,name,process_id,' . $process->id,
            'value' => 'sometimes|json',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:255',
        ]);
    
        // Check if the record belongs to the given process
        if ($record->process_id !== $process->id) {
            return response()->json(['message' => 'Record does not belong to the specified process'], 403);
        }
                
        // Change plain status name into ProcessStatus if provided
        if ($request->has('status')) {
            $statusName = $request->status;
            $processStatus = $process->processStatuses->firstWhere('name', $statusName);
            if (!$processStatus) {
                return response()->json(['message' => 'Invalid status provided'], 422);
            }
            $record->process_status_id = $processStatus->id;
        }
    
        // Update other fields if provided
        $record->fill($request->only(['run', 'type', 'reference']));
        $record->save();
    
        // Update value if provided
        if ($request->has('value')) {
            $record->addValue($request->value);
        }
    
        // Update tags if provided
        if ($request->has('tags')) {
            $record->updateTags($request->tags);
        }
        
        // $record->save();

        return new RecordResource($record);
    }

    public function destroy(Process $process, Record $record)
    {
        $record->delete();

        return response()->json(['message' => 'Success.']);
    }
}
