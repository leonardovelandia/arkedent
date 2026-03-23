<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_valoracion', 20)->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->onDelete('set null');
            $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha');
            $table->text('motivo_consulta');

            // Examen extraoral
            $table->text('extraoral_cara')->nullable();
            $table->text('extraoral_atm')->nullable();
            $table->text('extraoral_ganglios')->nullable();
            $table->text('extraoral_labios')->nullable();
            $table->text('extraoral_observaciones')->nullable();

            // Examen intraoral
            $table->text('intraoral_encias')->nullable();
            $table->text('intraoral_mucosa')->nullable();
            $table->text('intraoral_lengua')->nullable();
            $table->text('intraoral_paladar')->nullable();
            $table->enum('intraoral_higiene', ['excelente', 'buena', 'regular', 'mala'])->nullable();
            $table->text('intraoral_observaciones')->nullable();

            // Diagnóstico y plan
            $table->json('diagnosticos')->nullable();
            $table->json('plan_tratamiento')->nullable();
            $table->enum('pronostico', ['excelente', 'bueno', 'reservado', 'malo'])->nullable();
            $table->text('observaciones_generales')->nullable();

            $table->foreignId('presupuesto_id')->nullable()->constrained('presupuestos')->onDelete('set null');
            $table->enum('estado', ['en_proceso', 'completada', 'cancelada'])->default('en_proceso');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }
};
