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
        // entries: búsquedas frecuentes del vigilante y reportes
        Schema::table('entries', function (Blueprint $table) {
            $table->index('cedula');
            $table->index('plate');
            $table->index('created_at');
            $table->index(['condominium_id', 'created_at']);
        });

        // authorizations: validación al registrar ingreso
        Schema::table('authorizations', function (Blueprint $table) {
            $table->index('cedula');
            $table->index(['status', 'end_date']);
            $table->index(['user_id', 'status']);
        });

        // users: lookup por cédula/username
        Schema::table('users', function (Blueprint $table) {
            $table->index('cedula');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropIndex(['cedula']);
            $table->dropIndex(['plate']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['condominium_id', 'created_at']);
        });

        Schema::table('authorizations', function (Blueprint $table) {
            $table->dropIndex(['cedula']);
            $table->dropIndex(['status', 'end_date']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['cedula']);
        });
    }
};
