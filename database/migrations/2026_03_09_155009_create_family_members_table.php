<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // propietario o residente al que pertenece
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('cedula', 20)->unique();
            $table->string('phone', 15)->nullable();
            $table->enum('relationship', [
                'esposo', 'esposa', 'hijo', 'hija',
                'padre', 'madre', 'hermano', 'hermana', 'otro',
            ])->default('otro');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
