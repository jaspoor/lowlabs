<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Resources\ClientRecordResource;
use App\Models\Client;
use App\Models\ClientRecord;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientRecordController extends BaseController
{
    public function index(Request $request, Client $client)
    {
        $tagNames = Tag::pluck('name')->toArray();
        $tagParams = array_fill_keys($tagNames, 'max:255');

        $sortableFields = ['type', 'reference', 'created_at'];

        $validatedData = $request->validate([
            'reference' => 'max:255',
            'dir' => 'nullable|in:asc,desc', 
            'sort' => 'nullable|in:' . implode(',', $sortableFields),
            'created_before' => 'nullable|date',
            'created_after' => 'nullable|date',
            'created_between' => 'nullable|date_range',       
        ] + $tagParams);
        
        $query = ClientRecord
            ::query()
            ->belongsToUser($request->user());

        // Find by reference
        if (isset($validatedData['reference'])) {
            $query->whereIn('reference', explode(',', $validatedData['reference']));
        }

        // Find by tag(s)
        foreach ($tagNames as $tagName) {
            if (isset($validatedData[$tagName])) {
                $tagValues = explode(',', $validatedData[$tagName]);
                $query->whereHas('clientRecordTagValues.tag', function ($query) use ($tagName, $tagValues) {
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
        
        return ClientRecordResource::collection($query->latest()->paginate());
    }

    public function show(Client $client, ClientRecord $record)
    {
        return new ClientRecordResource($record);
    }

    public function store(Request $request, Client $client) {
        
        $request->validate([
            'type' => 'required|max:255',
            'reference' => 'max:255',
            'value' => 'json',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:255',
        ]);
                
        $user = $request->user();

        // Add record
        $record = new ClientRecord;
        $record->user_id = $user->id;
        $record->client_id = $user->client->id;
        $record->type = $request->type;
        $record->reference = $request->reference;
        $record->save();

        if ($request->value) {
            $record->addValue($request->value);
        }

        $record->updateTags($request->tags);
        
        return new ClientRecordResource($record);
    }

    public function update(Request $request, Client $client, ClientRecord $record) {
        
        $request->validate([
            'type' => 'sometimes|required|max:255',
            'reference' => 'sometimes|max:255',
            'value' => 'sometimes|json',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:255',
        ]);
    
        // Check if the record belongs to the given client
        if ($record->client_id !== $client->id) {
            return response()->json(['message' => 'Record does not belong to the specified client'], 403);
        }
                    
        // Update other fields if provided
        $record->fill($request->only(['type', 'reference']));
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

        return new ClientRecordResource($record);
    }

    public function destroy(Client $client, ClientRecord $record)
    {
        $record->delete();

        return response()->json(['message' => 'Success.']);
    }
}
