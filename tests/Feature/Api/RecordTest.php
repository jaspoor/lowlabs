<?php

namespace Tests\Feature\Api;

use App\Models\Record;
use App\Models\RecordValue;
use App\Models\TagValue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\ApiTestCase;

class RecordTest extends ApiTestCase
{
    public function test_api_record_index(): void
    {
        $this->createRecord();

        $this->json('GET', 
            sprintf('api/processes/%d/records', $this->process->id), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => ['*' => ['id',  'client_id',  'user_id',  'process_id',  'run',  'type',  
                    'reference', 'values', 'tags', 'status', 'updated_at',  'created_at', 'retain_days']
                ]])
            ->assertJsonPath('data.0.values', ['value1', 'value2'])
            ->assertJsonPath('data.0.tags', ['Location' => 'The Hague', 'Color' => 'Red'])
            ->assertJsonPath('data.0.status', 'new');
        
    }

    public function test_api_record_search_by_reference(): void
    {
        $record1 = $this->createRecord();
        $record2 = $this->createRecord();
        $record3 = $this->createRecord();

        $this->json('GET', 
            sprintf('api/processes/%d/records?reference=%s', $this->process->id, $record2->reference), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference', $record2->reference);
    }

    public function test_api_record_search_by_status(): void
    {
        $record1 = $this->createRecord();
        $record2 = $this->createRecord();
        $record3 = $this->createRecord();

        $record2->processStatus()->associate($this->process->processStatuses->last());
        $record2->save();

        $this->json('GET', 
            sprintf('api/processes/%d/records?status=%s', $this->process->id, $record2->processStatus->name), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference', $record2->reference);
    }

    public function test_api_record_search_by_tag(): void
    {
        $record1 = $this->createRecord();
        $record2 = $this->createRecord();
        $record3 = $this->createRecord();

        $record2->updateTags(['Location' => 'Rotterdam']);
        $record2->save();

        $this->json('GET', 
            sprintf('api/processes/%d/records?%s=%s', $this->process->id, 'Location', 'Rotterdam'), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference', $record2->reference);
    }

    public function test_api_record_store(): void
    {
        $value = '{ "test": "test" }';
        $status = $this->process->processStatuses->last()->name;

        $payload = [
            'process_id' => $this->process->id, 
            'run' => 'test-1', 
            'type' => 'test-2', 
            'reference' => 'test-3', 
            'value' => $value,
            'tags' => [
                'Location' => 'The Hague',
                'Address' => 'Binckhorstlaan 36' // New tag to create
            ],
            'status' => $status
        ];

        $this->json('POST', 
            sprintf('api/processes/%d/records', $this->process->id),
            $payload, 
            $this->getAuthorizationHeader())
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'client_id' => $this->user->client->id,
                    'user_id' => $this->user->id,
                    'process_id' => $this->process->id,
                    'run' => 'test-1', 
                    'type' => 'test-2', 
                    'reference' => 'test-3', 
                    'values' => [$value],
                    'tags' => [
                        'Location' => 'The Hague',
                        'Address' => 'Binckhorstlaan 36'
                    ],
                    'status' => $status,
                ]
            ]);
    }

    public function test_api_record_update(): void
    {
        $record1 = $this->createRecord();

        $value = '{ "test": "test" }';

        $payload = [
            'value' => $value
        ];

        $this->json('PATCH', 
            sprintf('api/processes/%d/records/%d', $this->process->id, $record1->id),
            $payload, 
            $this->getAuthorizationHeader())
            ->assertStatus(200);

        $this->json('GET', 
            sprintf('api/processes/%d/records/%d', $this->process->id, $record1->id), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonPath('data.values.2', $value);
    }

    public function test_api_record_destroy(): void
    {
        $record = $this->createRecord();

        $this->json('DELETE', 
            sprintf('api/processes/%d/records/%d', $this->process->id, $record->id), 
            [], 
            $this->getAuthorizationHeader())
            ->assertStatus(200);
            
        $this->assertModelMissing($record);
    }

    public function test_api_record_getting_a_high_id_should_work(): void
    {
        $records = $this->createRecord(100);

        $this->json('GET', 
            sprintf('api/processes/%d/records/%d', $this->process->id, $records->last()->id), 
            [], 
            $this->getAuthorizationHeader())
            ->assertStatus(200);
    }

    public function test_api_record_should_be_removed_automatically_after_retain_days(): void
    {
        // Create an old record
        $oldRecord = $this->createRecord();
        $oldRecord->created_at = now()->subDays(100); // Create record 100 days ago
        $oldRecord->retain_days = 90; // Retain for 90 days
        $oldRecord->save();

        // Ensure the record exists before the request
        $this->assertDatabaseHas('records', ['id' => $oldRecord->id]);

        // Perform a GET request to the record index        
        $response = $this->json('GET', 
            sprintf('api/processes/%d/records', $this->process->id), 
            [], 
            $this->getAuthorizationHeader())
            ->assertStatus(200)
            ->assertJsonMissing(['id' => $oldRecord->id]);
    }

    public function test_api_record_should_sort_by_created_at_desc(): void
    {
        // Create an old record
        $record1 = $this->createRecord();
        $record2 = $this->createRecord();
        $record3 = $this->createRecord();

        $record2->created_at = date('Y-m-d H:i:s', strtotime($record2->created_at . ' -1 day'));
        $record2->save();

        $record3->created_at = date('Y-m-d H:i:s', strtotime($record3->created_at . ' +1 day'));
        $record3->save();

        // Perform a GET request to the record index        
        $response = $this->json('GET', 
            sprintf('api/processes/%d/records?sort=created_at&dir=desc', $this->process->id), 
            [], 
            $this->getAuthorizationHeader())
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => $record3->id],
                    ['id' => $record1->id],
                    ['id' => $record2->id],
                ]
            ]);
    }

    private function createRecord(int $count = 1): Record|Collection
    {
        $records = Record
            ::factory()
            ->for($this->client)
            ->for($this->user)
            ->for($this->process)
            ->for($this->process->processStatuses->first())
            ->count($count)
            ->has(RecordValue::factory()
                ->sequence(
                    ['value' => 'value1'],
                    ['value' => 'value2']
                )
                ->count(2))
            ->has(TagValue::factory()
                ->sequence(
                    ['tag_id' => $this->tags->first()->id, 'value' => 'The Hague'],
                    ['tag_id' => $this->tags->last()->id, 'value' => 'Red']
                )
                ->count(2))
            ->create();

        return (1 === $count) ? $records->first() : $records;
    }
}
