<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show all users
    public function index(Client $client)
    {
        $users = $client->users()->get();
        return view('users.index', compact('client', 'users'));
    }

    // Show create user form
    public function create(Client $client)
    {
        return view('users.form', compact('client'));
    }

    // Store new user
    public function store(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Hash the password
        $hashedPassword = Hash::make($request->password);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hashedPassword,
            'client_id' => $client->id
        ]);

        return redirect()->route('users.index', ['client' => $client])->with('success', 'User created successfully');
    }

    // Show edit user form
    public function edit(User $user, Client $client)
    {
        return view('users.form', compact('user', 'client'));
    }

    // Update user
    public function update(Request $request, Client $client, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
        ]);

        $user->update($request->all());

        return redirect()->route('users.index', ['client' => $client]);
    }

    // Delete user
    public function destroy(Client $client, User $user)
    {
        $user->delete();
        
        return redirect()->route('users.index', ['client' => $client]);
    }
}
