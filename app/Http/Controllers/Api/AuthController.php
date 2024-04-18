<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Mail\ActivationCodeMailable;
use App\Models\Activation;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function request(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        $domain = substr(strrchr($email, "@"), 1);

        $client = Client::where('domain', $domain)->first();

        if ($client) {                

            $code = rand(100000, 999999);
            Activation::create([
                'email' => $email,
                'code' => $code,
                'client_id' => $client->id
            ]);

            Mail::to($email)->send(new ActivationCodeMailable($email, $code));
        }

        return response()->json("success");
    }

    public function activate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric'
        ]);

        $email = $request->input('email');
        $code = $request->input('code');

        $activation = Activation::where('email', $email)
                                ->where('code', $code)
                                ->where('created_at', '>=', now()->subHours(4))
                                ->first();

        if (!$activation) {
            return response()->json(['message' => 'Invalid or expired code'], 401);
        }

        $user = User::firstOrCreate([
            'email' => $email
        ], [
            'name' => '',
            'client_id' => $activation->client->id,
            'password' => Hash::make(Str::random(10)) // Generating a random password
        ]);

        $token = $user->createToken('authToken', ['plugin'])->plainTextToken;

        $activation->delete();
        
        return response()->json(['success' => true], 200)->header('Authorization', $token);
    }
}
