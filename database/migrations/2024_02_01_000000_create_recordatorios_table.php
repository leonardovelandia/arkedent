<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->enum('tipo', ['email', 'whatsapp', 'ambos']);
            $table->enum('canal', ['email', 'whatsapp']);
            $table->enum('estado', ['pendiente', 'enviado', 'fallido', 'cancelado'])->default('pendiente');
            $table->text('mensaje');
            $table->datetime('fecha_programada');
            $table->datetime('fecha_envio')->nullable();
            $table->text('respuesta_api')->nullable();
            $table->integer('intentos')->default(0);
            $table->text('error')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recordatorios');
    }
};
