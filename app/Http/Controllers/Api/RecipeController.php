<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Resources\RecipeResource;
use App\Models\Client;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends BaseController
{
    public function index(Request $request)
    {
        return RecipeResource::collection(Recipe
            ::belongsToClient($request->user()->client)
            ->latest()
            ->paginate());
    }

    public function show(Recipe $recipe)
    {
        return new RecipeResource($recipe);
    }

    public function store(Request $request)
    {        
        $request->validate([
            'name' => 'required|max:255',
            'config' => 'required'
        ]);

        $recipe = new Recipe($request->all());
        $recipe->client_id = $request->user()->client->id;
        $recipe->save();

        return new RecipeResource($recipe);
    }

    public function update(Request $request, Recipe $recipe) {
        
        $request->validate([
            'name' => 'required|max:255',
            'config' => 'required'
        ]);

        $recipe->update($request->all());
        
        return new RecipeResource($recipe);
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return $this->jsonSuccess();
    }
}
