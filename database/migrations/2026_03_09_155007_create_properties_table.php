<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20)->unique(); // Número: 101, A-302, Casa-5
            $table->string('block', 30)->nullable();  // Torre A, Bloque 2
            $table->enum('type', ['apartamento', 'casa', 'local'])->default('apartamento');
            $table->string('description', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
