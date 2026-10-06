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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animals')->onDelete('restrict');
            $table->uuid('user_id');
            $table->foreignId('replaces_id')->nullable()->constrained('medical_records')->onDelete('restrict');
            $table->enum('record_type', ['checkup', 'vaccination', 'deworming', 'emergency', 'correction']);
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->date('performed_at');
            $table->date('next_checkup_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
