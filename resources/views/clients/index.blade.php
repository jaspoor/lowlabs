<!-- Client management view file: resources/views/clients/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2>Manage Clients</h2>
    <a href="{{ url('/clients/create') }}" class="btn btn-primary">Add New Client</a>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Domain</th>
                <th></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->domain }}</td>
                <td>
                    <a href="{{ url('/clients/'.$client->id.'/users') }}" >Show users</a>
                </td>
                <td>
                    <a href="{{ url('/clients/edit/'.$client->id) }}" class="btn btn-info">Edit</a>
                    <form action="{{ url('/clients/'.$client->id) }}" method="POST" style="display: inline;">
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
