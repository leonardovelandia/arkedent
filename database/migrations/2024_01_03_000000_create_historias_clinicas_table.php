<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->date('fecha_apertura');
            $table->text('motivo_consulta');
            $table->text('enfermedad_actual')->nullable();
            $table->text('antecedentes_medicos')->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->text('alergias')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('antecedentes_odontologicos')->nullable();
            $table->text('habitos')->nullable();
            $table->string('presion_arterial', 20)->nullable();
            $table->string('frecuencia_cardiaca', 20)->nullable();
            $table->string('temperatura', 10)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->json('odontograma')->nullable();
            $table->text('observaciones_generales')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
