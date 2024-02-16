<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Process;
use App\Models\ProcessStatus;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    /**
     * @var string Token to use in Authorization header
     */
    protected $token = null;

    /**
     * @var Client client Mocked Client object
     */
    protected $client;

    /**
     * @var User user Mocked User object
     */
    protected $user;

    /**
     * @var Process process Mocked Process object
     */
    protected $process;

    /**
     * @var Collection<Tag> list of mocked Tag objects
     */
    protected $tags;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = Client::factory()->create();

        $this->user = User::factory()
            ->for($this->client)
            ->create([
                'email' => 'test@example.com',
                'password' => bcrypt('test'),
            ]);
            
        $this->process = Process::factory()
            ->for($this->client)
            ->has(ProcessStatus::factory()
                ->sequence(
                    ['name' => 'new'],
                    ['name' => 'pending'],
                    ['name' => 'done'],                    
                )
                ->count(3)
            )
            ->create();

        $this->tags = Tag::factory()
            ->sequence(
                ['name' => 'Location'],
                ['name' => 'Phonenumber'],
                ['name' => 'Color'],
            )
            ->count(3)
            ->create();
            
        $this->token = $this->user
            ->createToken('test', ['api'])
            ->plainTextToken;
    }

    protected function getAuthorizationHeader(): array 
    {    
        return [
            'Authorization' => 'Bearer ' . $this->token
        ];
    }
}
