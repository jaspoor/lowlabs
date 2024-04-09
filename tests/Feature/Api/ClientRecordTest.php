<?php

namespace Tests\Feature\Api;

use App\Models\ClientRecord;
use App\Models\ClientRecordTagValue;
use App\Models\ClientRecordValue;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\ApiTestCase;

class ClientRecordTest extends ApiTestCase
{
    public function test_api_client_record_index(): void
    {
        $this->createClientRecord();

        $this->json('GET', 
            sprintf('api/clients/%d/records', $this->client->id), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => ['*' => ['id',  'client_id',  'user_id',  'type',  
                    'reference', 'values', 'tags', 'updated_at',  'created_at']
                ]])
            ->assertJsonPath('data.0.values', ['value1', 'value2'])
            ->assertJsonPath('data.0.tags', ['Location' => 'The Hague', 'Color' => 'Red']);
        
    }

    public function test_api_client_record_search_by_reference(): void
    {
        $record1 = $this->createClientRecord();
        $record2 = $this->createClientRecord();
        $record3 = $this->createClientRecord();

        $this->json('GET', 
            sprintf('api/clients/%d/records?reference=%s', $this->client->id, $record2->reference), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference', $record2->reference);
    }

    public function test_api_client_record_search_by_tag(): void
    {
        $record1 = $this->createClientRecord();
        $record2 = $this->createClientRecord();
        $record3 = $this->createClientRecord();

        $record2->updateTags(['Location' => 'Rotterdam']);
        $record2->save();

        $this->json('GET', 
            sprintf('api/clients/%d/records?%s=%s', $this->client->id, 'Location', 'Rotterdam'), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference', $record2->reference);
    }

    public function test_api_client_record_store(): void
    {
        $value = '{ "test": "test" }';

        $payload = [
            'type' => 'test-1', 
            'reference' => 'test-2', 
            'value' => $value,
            'tags' => [
                'Location' => 'The Hague',
                'Address' => 'Binckhorstlaan 36' // New tag to create
            ]
        ];

        $this->json('POST', 
            sprintf('api/clients/%d/records', $this->client->id),
            $payload, 
            $this->getAuthorizationHeader())
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'client_id' => $this->user->client->id,
                    'user_id' => $this->user->id,
                    'type' => 'test-1', 
                    'reference' => 'test-2', 
                    'values' => [$value],
                    'tags' => [
                        'Location' => 'The Hague',
                        'Address' => 'Binckhorstlaan 36'
                    ]
                ]
            ]);
    }

    public function test_api_client_record_update(): void
    {
        $record1 = $this->createClientRecord();

        $value = '{ "test": "test" }';

        $payload = [
            'value' => $value
        ];

        $this->json('PATCH', 
            sprintf('api/clients/%d/records/%d', $this->client->id, $record1->id),
            $payload, 
            $this->getAuthorizationHeader())
            ->assertStatus(200);

        $this->json('GET', 
            sprintf('api/clients/%d/records/%d', $this->client->id, $record1->id), [], 
            $this->getAuthorizationHeader()
        )
            ->assertStatus(200)
            ->assertJsonPath('data.values.2', $value);
    }

    public function test_api_client_record_destroy(): void
    {
        $record = $this->createClientRecord();

        $this->json('DELETE', 
            sprintf('api/clients/%d/records/%d', $this->client->id, $record->id), 
            [], 
            $this->getAuthorizationHeader())
            ->assertStatus(200);
            
        $this->assertModelMissing($record);
    }

    public function test_api_client_record_getting_a_high_id_should_work(): void
    {
        $records = $this->createClientRecord(100);

        $this->json('GET', 
            sprintf('api/clients/%d/records/%d', $this->client->id, $records->last()->id), 
            [], 
            $this->getAuthorizationHeader())
            ->assertStatus(200);
    }

    public function test_api_client_record_should_sort_by_created_at_desc(): void
    {
        // Create an old record
        $record1 = $this->createClientRecord();
        $record2 = $this->createClientRecord();
        $record3 = $this->createClientRecord();

        $record2->created_at = date('Y-m-d H:i:s', strtotime($record2->created_at . ' -1 day'));
        $record2->save();

        $record3->created_at = date('Y-m-d H:i:s', strtotime($record3->created_at . ' +1 day'));
        $record3->save();

        // Perform a GET request to the record index        
        $response = $this->json('GET', 
            sprintf('api/clients/%d/records?sort=created_at&dir=desc', $this->client->id), 
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

    private function createClientRecord(int $count = 1): ClientRecord|Collection
    {
        $records = ClientRecord
            ::factory()
            ->for($this->client)
            ->for($this->user)
            ->count($count)
            ->has(ClientRecordValue::factory()
                ->sequence(
                    ['value' => 'value1'],
                    ['value' => 'value2']
                )
                ->count(2))
            ->has(ClientRecordTagValue::factory()
                ->sequence(
                    ['tag_id' => $this->tags->first()->id, 'value' => 'The Hague'],
                    ['tag_id' => $this->tags->last()->id, 'value' => 'Red']
                )
                ->count(2))
            ->create();

        return (1 === $count) ? $records->first() : $records;
    }
}
