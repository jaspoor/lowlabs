<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecordCollection;
use App\Models\User;
Use App\Models\Record;
Use App\Http\Resources\RecordResource;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $records = Record::whereBelongsTo($user)->latest()->paginate();

        return new RecordCollection($records);
    }

    public function store(Request $request) {
        $user = $request->user();
        
        $record = new Record;
        $record->user_id = $user->id;
        $record->type = $request->type;
        $record->data = $request->data;
        $record->save();

        return response()->json(['message' => 'Record stored.']);
    }

    public function show(Record $record)
    {
        return new RecordResource($record);
    }

    public function delete(Record $record)
    {
        $record->delete();

        return response()->json(['message' => 'Record removed.']);
    }

}
