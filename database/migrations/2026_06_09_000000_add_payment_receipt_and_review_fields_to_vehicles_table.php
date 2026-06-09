<?php

use App\Models\Vehicle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('payment_receipt_path')->nullable()->after('registration_document_path');
            $table->string('review_status')->default('pending')->after('payment_receipt_path');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('review_status');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('rejection_reason')->nullable()->after('reviewed_at');
        });

        Vehicle::whereHas('registrations', function ($query) {
            $query->where('status', 'approved');
        })->update([
            'review_status' => 'approved',
        ]);
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'payment_receipt_path',
                'review_status',
                'reviewed_by',
                'reviewed_at',
                'rejection_reason',
            ]);
        });
    }
};
