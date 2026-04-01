<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fichas_periodontales', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ficha', 20)->unique();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->onDelete('set null');
            $table->foreignId('user_id')->constrained();
            $table->date('fecha_inicio');
            // Índice de placa
            $table->decimal('indice_placa_porcentaje', 5, 2)->nullable();
            $table->json('indice_placa_datos')->nullable();
            $table->date('fecha_indice_placa')->nullable();
            // Índice gingival
            $table->decimal('indice_gingival_porcentaje', 5, 2)->nullable();
            $table->json('indice_gingival_datos')->nullable();
            $table->date('fecha_indice_gingival')->nullable();
            // Sondaje
            $table->json('sondaje_datos')->nullable();
            $table->date('fecha_sondaje')->nullable();
            // Diagnóstico
            $table->enum('clasificacion_periodontal', [
                'salud_periodontal','gingivitis_inducida_placa','gingivitis_no_inducida_placa',
                'periodontitis_estadio_i','periodontitis_estadio_ii','periodontitis_estadio_iii','periodontitis_estadio_iv',
                'periodontitis_necrosante','absceso_periodontal','lesion_endoperio','deformidades_condiciones'
            ])->nullable();
            $table->enum('extension', ['localizada','generalizada'])->nullable();
            $table->enum('severidad', ['leve','moderada','severa'])->nullable();
            $table->json('factores_riesgo')->nullable();
            $table->text('diagnostico_texto')->nullable();
            $table->text('plan_tratamiento')->nullable();
            $table->enum('pronostico_general', ['excelente','bueno','regular','malo','sin_esperanza'])->nullable();
            $table->json('pronostico_por_diente')->nullable();
            // Estado
            $table->enum('estado', ['activa','en_tratamiento','mantenimiento','finalizada','abandonada'])->default('activa');
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fichas_periodontales'); }
};
