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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('habitat_id');
            $table->unsignedBigInteger('species_id');
            $table->string('code', 100)->unique();
            $table->string('name', 100);
            $table->enum('sex', ['male', 'female', 'unknown'])->default('unknown');
            $table->date('birth_date')->nullable();
            $table->date('admission_date')->useCurrent();
            $table->enum('health_status', ['healthy', 'treatment', 'quarantine'])->default('healthy');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('habitat_id')->references('id')->on('habitats')->onDelete('restrict');
            $table->foreign('species_id')->references('id')->on('species')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function(Blueprint $table) {
            $table->dropForeign(['habitat_id', 'species_id']);
        });
        Schema::dropIfExists('animals');
    }
};
