<!-- User management view file: resources/views/users/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="py-4">
    <h2>Manage Users</h2>
    <a href="{{ url('/clients/'.$client->id.'/users/create') }}" class="btn btn-primary">Add New User</a>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ url('/clients/'.$client->id.'/users/edit/'.$user->id) }}" class="btn btn-info">Edit</a>
                    <form action="{{ url('/clients/'.$client->id.'/users/'.$user->id) }}" method="POST" style="display: inline;">
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
