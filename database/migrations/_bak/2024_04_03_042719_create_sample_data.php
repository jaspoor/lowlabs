<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('clients')->insert([
            'name' => 'Sample Client',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('users')->insert([
            'name' => 'Sample User',
            'email' => 'sample@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // Replace 'password' with the actual password
            'client_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('processes')->insert([
            'name' => 'Sample Process',
            'client_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('process_statuses')->insert([
            'name' => 'Sample Status',
            'process_id' => 1,
        ]);

        DB::table('records')->insert([
            'run' => 'SampleRun',
            'type' => 'SampleType',
            'reference' => 'SampleReference',
            'data' => 'SampleData',
            'user_id' => 1,
            'client_id' => 1,
            'process_id' => 1,
            'process_status_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('record_values')->insert([
            'value' => json_encode(['key' => 'value']),
            'record_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('tags')->insert([
            'name' => 'Sample Tag',
        ]);

        DB::table('tag_values')->insert([
            'value' => 'Sample Value',
            'tag_id' => 1,
            'record_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_data');
    }
};
