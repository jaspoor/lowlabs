<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    // Show all recipes
    public function index(Client $client)
    {
        $recipes = $client->recipes()->get();
        return view('recipes.index', compact('client', 'recipes'));
    }

    // Show create recipe form
    public function create(Client $client)
    {
        return view('recipes.form', compact('client'));
    }

    // Store new recipe
    public function store(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'config' => 'required',
        ]);

        $recipe = new Recipe($request->all());
        $recipe->client_id = $request->user()->client->id;
        $recipe->save();

        return redirect()->route('recipes.index', ['client' => $client]);
    }

    // Show edit recipe form
    public function edit(Client $client, Recipe $recipe)
    {
        return view('recipes.form', compact('client', 'recipe'));
    }

    // Update recipe
    public function update(Request $request, Client $client, Recipe $recipe)
    {
        $request->validate([
            'name' => 'required',
            'config' => 'required',
        ]);

        $recipe->update($request->all());

        return redirect()->route('recipes.index', ['client' => $client]);
    }

    // Delete recipe
    public function destroy(Client $client, Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('recipes.index', ['client' => $client]);
    }
}
