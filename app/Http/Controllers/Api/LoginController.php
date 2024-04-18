<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::firstWhere('email', $request->email);
            $expirationTime = $request->boolean('remember_me') ? 
                now()->addMonths(6) : now()->addHours(24);

            $token = $user->createToken('web', ['api'], $expirationTime);

            return response()->json([
                'token' => $token->plainTextToken,
                'expires_at' => $token->accessToken->expires_at,
                'abilities' => $token->accessToken->abilities,
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
}
