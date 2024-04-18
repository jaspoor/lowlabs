<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    public function index() 
    {
        return TagResource::collection(Tag::latest()->paginate());
    }

    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    public function store(Request $request)
    {        
        $request->validate([
            'name' => 'required|max:255'
        ]);

        $tag = Tag::create($request->all());

        return new TagResource($tag);
    }

    public function update(Request $request, Tag $tag) {
        
        $request->validate([
            'name' => 'required'
        ]);

        $tag->update($request->all());

        return new TagResource($tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'Success.']);
    }
}
