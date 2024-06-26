<?php

namespace Tests\Feature\Api;

use App\Models\ProcessRecord;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\ApiTestCase;

class ProcessTest extends ApiTestCase
{
    public function test_api_process_update(): void
    {
        $records = ProcessRecord
            ::factory()
            ->for($this->client)
            ->for($this->user)
            ->for($this->process)
            ->for($this->process->processStatuses->first())
            ->count(2)
            ->create();

        $this->json('GET', sprintf('api/processes/%d/records', $this->process->id), [], 
            $this->getAuthorizationHeader())
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Change name and add status
        $this->json('PATCH', sprintf('api/processes/%d', $this->process->id),
            ['name' => 'Updated', 'statuses' => [$this->process->processStatuses->first()->name, 'test2']],
            $this->getAuthorizationHeader())
            ->assertStatus(200);

        // Ensure records are still attached
        $this->json('GET', sprintf('api/processes/%d/records', $this->process->id), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $this->json('GET', sprintf('api/processes/%d', $this->process->id), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $this->process->id,
                'name' => 'Updated'
            ]]);
    }
}
