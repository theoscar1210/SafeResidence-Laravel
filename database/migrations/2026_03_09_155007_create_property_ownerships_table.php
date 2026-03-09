<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_ownerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // rol: Propietario
            $table->date('since_date')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'user_id']); // sin duplicados
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_ownerships');
    }
};
