<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('cedula', 20);
            $table->string('apartment', 20);
            $table->enum('type', ['propietario', 'autorizado', 'visitante'])->default('visitante');
            $table->enum('vehicle', ['automovil', 'camioneta', 'moto', 'bicicleta', 'ninguno'])->default('ninguno');
            $table->string('registered_by', 100)->nullable();
            $table->text('observations')->nullable();
            $table->dateTime('entry_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
