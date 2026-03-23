<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes_clinicas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_imagen', 20)->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->onDelete('set null');
            $table->foreignId('evolucion_id')->nullable()->constrained('evoluciones')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipo', [
                'fotografia_intraoral',
                'fotografia_extraoral',
                'radiografia_periapical',
                'radiografia_panoramica',
                'radiografia_bitewing',
                'foto_antes',
                'foto_durante',
                'foto_despues',
                'foto_sonrisa',
                'otra',
            ]);
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->string('archivo_path', 255);
            $table->string('archivo_nombre', 255);
            $table->string('archivo_tipo', 50);
            $table->integer('archivo_tamanio')->nullable();
            $table->string('diente', 20)->nullable();
            $table->date('fecha_toma');
            $table->boolean('es_comparativo')->default(false);
            $table->string('grupo_comparativo', 50)->nullable();
            $table->enum('orden_comparativo', ['antes', 'durante', 'despues'])->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes_clinicas');
    }
};
