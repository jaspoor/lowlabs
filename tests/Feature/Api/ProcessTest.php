<?php

namespace Tests\Feature\Api;

use App\Models\Record;
use App\Models\RecordValue;
use App\Models\TagValue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\ApiTestCase;

class ProcessTest extends ApiTestCase
{
    public function test_api_process_update(): void
    {
        $records = Record
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

        $this->json('PATCH', sprintf('api/processes/%d', $this->process->id),
            ['name' => 'Updated'], $this->getAuthorizationHeader())
            ->assertStatus(200);

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
