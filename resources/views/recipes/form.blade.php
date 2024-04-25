@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ isset($recipe) ? 'Edit Recipe' : 'Create Recipe' }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ isset($recipe) ? route('recipes.update', ['client' => $client->id, 'recipe' => $recipe->id]) : route('recipes.store', $client->id) }}">
                        @csrf
                        @if(isset($recipe))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', isset($recipe) ? $recipe->name : '') }}" required autofocus>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="config" class="form-label">Config</label>
                            <input type="text" class="form-control @error('config') is-invalid @enderror" id="config" name="config" value="{{ old('config', isset($recipe) ? $recipe->config : '') }}" required>

                            @error('config')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">{{ isset($recipe) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('recipes.index', $client->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
