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
        Schema::rename('records', 'process_records');
        Schema::rename('record_values', 'process_record_values');
        Schema::rename('tag_values', 'process_record_tag_values');

        Schema::table('process_record_values', function (Blueprint $table) {
            $table->renameColumn('record_id', 'process_record_id');
        });
        Schema::table('process_record_tag_values', function (Blueprint $table) {
            $table->renameColumn('record_id', 'process_record_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('process_records', 'records');
        Schema::rename('process_record_values', 'record_values');
        Schema::rename('process_record_tag_values', 'tag_values');

        Schema::table('record_values', function (Blueprint $table) {
            $table->renameColumn('process_record_id', 'record_id');
        });
        Schema::table('tag_values', function (Blueprint $table) {
            $table->renameColumn('process_record_id', 'record_id');
        });
    }
};
