<!-- Recipe management view file: resources/views/recipes/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2>Manage Recipes</h2>
    <a href="{{ url('/clients/'.$client->id.'/recipes/create') }}" class="btn btn-primary">Add New Recipe</a>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recipes as $recipe)
            <tr>
                <td>{{ $recipe->id }}</td>
                <td>{{ $recipe->config }}</td>
                <td>
                    <a href="{{ url('/clients/'.$client->id.'/recipes/edit/'.$recipe->id) }}" class="btn btn-info">Edit</a>
                    <form action="{{ url('/clients/'.$client->id.'/recipes/'.$recipe->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
