<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas_medicas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_receta', 20)->unique();

            // Relaciones
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('evolucion_id')->nullable()->constrained('evoluciones')->nullOnDelete();

            // Datos de la receta
            $table->date('fecha');
            $table->string('diagnostico', 500)->nullable();
            $table->json('medicamentos')->nullable();        // [{nombre, presentacion, dosis, frecuencia, duracion, cantidad, indicaciones}]
            $table->text('indicaciones_generales')->nullable();

            // Firma digital
            $table->boolean('firmado')->default(false);
            $table->longText('firma_data')->nullable();     // base64 canvas
            $table->datetime('fecha_firma')->nullable();
            $table->string('ip_firma', 50)->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('paciente_id');
            $table->index('fecha');
            $table->index('firmado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas_medicas');
    }
};
