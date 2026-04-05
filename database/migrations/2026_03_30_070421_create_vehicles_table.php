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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained();
            $table->string('registration_number')->unique(); // Plate number
            $table->string('color');
            $table->string('manufacturer');
            $table->string('model');
            $table->year('year')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('registration_document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
