<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_record_tag_values', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->timestamps();
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('client_record_id');
            
            // Foreign keys
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            $table->foreign('client_record_id')->references('id')->on('client_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_record_tag_values');
    }
};
