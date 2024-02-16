<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Process;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $client = Client::factory()
            ->create(['name' => 'Lowlabs']);

        $users = User::factory()
            ->count(2)
            ->for($client)
            ->create();
        
        $processes = Process::factory()
            ->count(2)
            ->for($client)
            ->hasProcessStatuses(4)
            ->create();

        $tags = Tag::factory()
            ->count(4)
            ->create();
    }
}
