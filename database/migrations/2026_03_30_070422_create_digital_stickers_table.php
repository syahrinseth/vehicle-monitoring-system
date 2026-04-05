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
        Schema::create('digital_stickers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->string('qr_code_token')->unique();
            $table->string('qr_code_image_path')->nullable();
            $table->date('validity_start_date');
            $table->date('validity_end_date');
            $table->enum('status', ['valid', 'expired', 'revoked'])->default('valid');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_stickers');
    }
};
