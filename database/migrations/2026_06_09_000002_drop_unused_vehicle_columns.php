<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'manufacturer',
                'year',
                'engine_number',
                'chassis_number',
                'registration_document_path',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('manufacturer')->nullable()->after('color');
            $table->year('year')->nullable()->after('model');
            $table->string('engine_number')->nullable()->after('year');
            $table->string('chassis_number')->nullable()->after('engine_number');
            $table->string('registration_document_path')->nullable()->after('chassis_number');
        });
    }
};
