<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar tabla apartments (reemplazada por properties + property_ownerships)
        Schema::dropIfExists('apartments');

        // Eliminar apartment_number de users (ya no necesario, se obtiene por relaciones)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('apartment_number');
        });
    }

    public function down(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('apartment_number', 20)->nullable()->after('phone');
        });
    }
};
