<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichas_ortodonticas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ficha', 20)->unique()->nullable();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->onDelete('set null');
            $table->foreignId('user_id')->constrained()->comment('Ortodoncista responsable');
            $table->date('fecha_inicio');
            $table->date('fecha_fin_estimada')->nullable();
            $table->date('fecha_fin_real')->nullable();
            $table->integer('duracion_meses_estimada')->nullable();

            // Análisis facial
            $table->enum('perfil', ['convexo', 'recto', 'concavo'])->nullable();
            $table->enum('simetria_facial', ['simetrica', 'asimetrica'])->nullable();
            $table->enum('biotipo_facial', ['dolicofacial', 'mesofacial', 'braquifacial'])->nullable();
            $table->text('analisis_facial_notas')->nullable();

            // Análisis dental
            $table->enum('clase_molar_derecha', ['clase_i', 'clase_ii', 'clase_iii'])->nullable();
            $table->enum('clase_molar_izquierda', ['clase_i', 'clase_ii', 'clase_iii'])->nullable();
            $table->enum('clase_canina_derecha', ['clase_i', 'clase_ii', 'clase_iii'])->nullable();
            $table->enum('clase_canina_izquierda', ['clase_i', 'clase_ii', 'clase_iii'])->nullable();
            $table->decimal('overjet', 4, 1)->nullable()->comment('Resalte en mm');
            $table->decimal('overbite', 4, 1)->nullable()->comment('Sobremordida en mm');
            $table->enum('linea_media_superior', ['centrada', 'desviada_derecha', 'desviada_izquierda'])->nullable();
            $table->enum('linea_media_inferior', ['centrada', 'desviada_derecha', 'desviada_izquierda'])->nullable();
            $table->decimal('desviacion_mm', 4, 1)->nullable();
            $table->enum('apinamiento_superior', ['leve', 'moderado', 'severo', 'ninguno'])->nullable();
            $table->enum('apinamiento_inferior', ['leve', 'moderado', 'severo', 'ninguno'])->nullable();
            $table->boolean('espaciamiento_superior')->default(false);
            $table->boolean('espaciamiento_inferior')->default(false);
            $table->boolean('mordida_cruzada_anterior')->default(false);
            $table->boolean('mordida_cruzada_posterior')->default(false);
            $table->boolean('mordida_abierta')->default(false);
            $table->boolean('mordida_profunda')->default(false);

            // Tratamiento
            $table->enum('tipo_ortodoncia', ['fija_metal', 'fija_estetica', 'fija_autoligado', 'removible', 'alineadores'])->nullable();
            $table->string('marca_brackets', 100)->nullable();
            $table->json('extracciones_indicadas')->nullable();
            $table->json('odontograma_ortodoncia')->nullable();
            $table->string('arco_inicial_superior', 50)->nullable();
            $table->string('arco_inicial_inferior', 50)->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('plan_tratamiento')->nullable();
            $table->enum('pronostico', ['excelente', 'bueno', 'reservado'])->nullable();
            $table->decimal('costo_total', 12, 2)->nullable();
            $table->enum('estado', ['diagnostico', 'activo', 'retencion', 'finalizado', 'cancelado'])->default('diagnostico');
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichas_ortodonticas');
    }
};
