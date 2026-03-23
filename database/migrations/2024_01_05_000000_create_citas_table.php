<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('procedimiento', 255);
            $table->enum('estado', ['pendiente','confirmada','en_proceso','atendida','cancelada','no_asistio'])->default('pendiente');
            $table->string('motivo_cancelacion', 255)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('recordatorio_enviado')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
