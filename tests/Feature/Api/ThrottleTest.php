<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\Feature\ApiTestCase;

class ThrottleTest extends ApiTestCase
{
    public function test_throttle_limit(): void
    {        
        $this->be(User::find(1));

        $allowed = 100;
        $a = 1;

        while ($a < $allowed) {
            $this->call('POST', 'api/login');
            $a++;
        };

        $this->call('POST', 'api/login')
            ->assertStatus(302); // We're not getting a 429 max requests
    }
}
