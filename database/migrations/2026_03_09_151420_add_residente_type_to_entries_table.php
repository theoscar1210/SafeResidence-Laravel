<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only MySQL enforces ENUM; SQLite stores as TEXT and accepts any value
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE entries MODIFY COLUMN type ENUM('propietario','residente','autorizado','visitante') NOT NULL DEFAULT 'visitante'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE entries MODIFY COLUMN type ENUM('propietario','autorizado','visitante') NOT NULL DEFAULT 'visitante'");
        }
    }
};
