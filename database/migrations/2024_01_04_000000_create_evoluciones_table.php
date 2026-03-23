<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->string('procedimiento', 255);
            $table->text('descripcion');
            $table->json('materiales')->nullable();
            $table->string('presion_arterial', 20)->nullable();
            $table->string('frecuencia_cardiaca', 20)->nullable();
            $table->date('proxima_cita_fecha')->nullable();
            $table->string('proxima_cita_procedimiento', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('dientes_tratados', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evoluciones');
    }
};
