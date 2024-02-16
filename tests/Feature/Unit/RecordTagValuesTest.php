<?php

namespace Tests\Feature\Unit;

use App\Models\Client;
use App\Models\Process;
use App\Models\ProcessStatus;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RecordTagValuesTest extends TestCase
{
    public function test_record_should_add_tag_value(): void
    {        
        $record = $this->createRecord();

        $this->assertNull($record->tagValues()->first());

        $tags = ['Location' => 'London'];

        $record->updateTags($tags);
        $record->refresh();

        $this->assertEquals($tags, $record->getTagsAssocArray());
    }

    public function test_record_should_update_existing_tag_value(): void
    {        
        $record = $this->createRecord();

        $this->assertNull($record->tagValues()->first());

        $tags = ['Location' => 'London'];

        $record->updateTags($tags);
        $record->refresh();

        $this->assertEquals($tags, $record->getTagsAssocArray());

        $tags = ['Location' => 'New York'];

        $record->updateTags($tags);
        $record->refresh();

        $this->assertEquals($tags, $record->getTagsAssocArray());
    }

    public function test_record_should_remove_missing_tag_value(): void
    {        
        $record = $this->createRecord();

        $this->assertNull($record->tagValues()->first());

        $tags = ['Location' => 'London'];

        $record->updateTags($tags);
        $record->refresh();

        $this->assertEquals($tags, $record->getTagsAssocArray());

        $tags = ['Color' => 'Green'];

        $record->updateTags($tags);
        $record->refresh();

        $this->assertEquals($tags, $record->getTagsAssocArray());
    }

    private function createRecord(): Record
    {
        $client = Client::factory()->create();
        $user = User::factory()->for($client)->create();
        $process = Process::factory()->for($client)->create();
        $processStatus = ProcessStatus::factory()->for($process)->create();
    
        $record = Record::factory()
            ->for($client)
            ->for($user)
            ->for($process)
            ->for($processStatus)
            ->create();
            
        return $record;
    }
}