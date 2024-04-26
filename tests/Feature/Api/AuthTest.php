<?php

namespace Tests\Feature\Api;

use App\Mail\ActivationCodeMailable;
use App\Models\Activation;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    //use RefreshDatabase, WithFaker, DatabaseTransactions;
    use WithFaker, DatabaseTransactions;

    public function test_can_request_auth_code()
    {     
        $domain = 'test.com';
        $email = 'user@' . $domain;

        Mail::fake();
        Client::factory()->create([
            'domain' => $domain
        ]);     

        $response = $this->json('POST', '/api/auth/request', ['email' => $email]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('activations', [
            'email' => $email
        ]);

        // Assert an email was sent to the specific user
        Mail::assertSent(ActivationCodeMailable::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });    
    }

    public function test_activate_with_valid_code()
    {
        $client = Client::create([
            'name' => 'Test Client',
            'domain' => 'test.com'
        ]);

        $activation = Activation::create([
            'email' => 'user@test.com',
            'code' => '123456',
            'client_id' => $client->id,
            'created_at' => now()
        ]);

        $response = $this->postJson('/api/auth/activate', [
            'email' => 'user@test.com',
            'code' => '123456'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['token_type' => 'bearer']);

        $this->assertDatabaseHas('users', [
            'email' => 'user@test.com'
        ]);
    }

    public function test_activate_with_invalid_code()
    {
        $client = Client::create([
            'name' => 'Test Client',
            'domain' => 'test.com'
        ]);

        $activation = Activation::create([
            'email' => 'user@test.com',
            'code' => '123456',
            'client_id' => $client->id,
        ]);

        // Expire code
        $activation->created_at = date('Y-m-d H:i:s', strtotime($activation->created_at . ' -5 hours'));
        $activation->save();

        $response = $this->postJson('/api/auth/activate', [
            'email' => 'user@test.com',
            'code' => '123456'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Invalid or expired code']);
    }
}