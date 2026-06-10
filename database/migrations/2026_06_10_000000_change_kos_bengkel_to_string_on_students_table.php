<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('kos_bengkel')->nullable()->change();
        });

        DB::table('students')
            ->whereIn('kos_bengkel', ['150', '150.0', '150.00'])
            ->update(['kos_bengkel' => 'DIPLOMA KOMPUTER SISTEM']);
    }

    public function down(): void
    {
        DB::table('students')->update(['kos_bengkel' => null]);

        Schema::table('students', function (Blueprint $table) {
            $table->decimal('kos_bengkel', 10, 2)->nullable()->change();
        });
    }
};
