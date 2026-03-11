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
        $tables = ['users', 'properties', 'entries', 'authorizations', 'announcements'];

        foreach ($tables as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->foreignId('condominium_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('condominiums')
                    ->nullOnDelete();
                $table->index('condominium_id');
            });
        }
    }

    public function down(): void
    {
        $tables = ['users', 'properties', 'entries', 'authorizations', 'announcements'];

        foreach ($tables as $tbl) {
            Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                $table->dropForeign(["{$tbl}_condominium_id_foreign"]);
                $table->dropIndex(["{$tbl}_condominium_id_index"]);
                $table->dropColumn('condominium_id');
            });
        }
    }
};
