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
        Schema::create('check_in_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('digital_sticker_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guard_id')->constrained('users');
            $table->string('scan_method')->default('qr'); // qr or plate
            $table->boolean('access_granted')->default(false);
            $table->string('denial_reason')->nullable();
            $table->string('scanner_ip')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('scanned_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_in_logs');
    }
};
