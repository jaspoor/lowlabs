<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Http\Record;
Use App\Http\Resources\RecordResource;

class RecordController extends Controller
{
    public function list(User $user) {
        
        return RecordResource::collection(Record::findByUser($user));
    }

    public function add() {
        return '';
    }
}
