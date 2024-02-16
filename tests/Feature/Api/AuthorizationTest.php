<?php

namespace Tests\Feature\API;

use Tests\Feature\ApiTestCase;

class AuthorizationTest extends ApiTestCase
{
    public function test_api_user_unauthorized_fails()
    {
        $this->json('GET', 'api/user')
            ->assertStatus(401);
    }

    public function test_api_user_authorized_succeeds()
    {
        $this->json('GET', 'api/user', [], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'created_at',
                'updated_at'
            ]);
    }
}
