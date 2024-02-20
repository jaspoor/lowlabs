<?php

namespace Tests\Feature\Api;

use App\Models\Record;
use App\Models\RecordValue;
use App\Models\TagValue;
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
                    'reference', 'values', 'tags', 'status', 'updated_at',  'created_at']
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

    private function createRecord(): Record
    {
        return Record
            ::factory()
            ->for($this->client)
            ->for($this->user)
            ->for($this->process)
            ->for($this->process->processStatuses->first())
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
    }
}
