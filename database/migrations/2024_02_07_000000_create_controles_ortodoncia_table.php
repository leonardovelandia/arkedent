<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controles_ortodoncia', function (Blueprint $table) {
            $table->id();
            $table->string('numero_control', 20)->unique()->nullable();
            $table->foreignId('ficha_ortodontica_id')->constrained('fichas_ortodonticas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained();
            $table->foreignId('cita_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained();
            $table->date('fecha_control');
            $table->integer('numero_sesion');
            $table->string('arco_superior', 100)->nullable();
            $table->string('arco_inferior', 100)->nullable();
            $table->enum('tipo_arco_superior', ['niti', 'acero', 'tma', 'fibra_vidrio', 'ninguno'])->nullable();
            $table->enum('tipo_arco_inferior', ['niti', 'acero', 'tma', 'fibra_vidrio', 'ninguno'])->nullable();
            $table->string('calibre_superior', 30)->nullable();
            $table->string('calibre_inferior', 30)->nullable();
            $table->enum('ligadura_superior', ['elastica', 'metalica', 'autoligado', 'ninguna'])->nullable();
            $table->enum('ligadura_inferior', ['elastica', 'metalica', 'autoligado', 'ninguna'])->nullable();
            $table->string('color_ligadura', 50)->nullable();
            $table->boolean('elasticos')->default(false);
            $table->string('tipo_elasticos', 100)->nullable();
            $table->json('brackets_reemplazados')->nullable();
            $table->json('odontograma_sesion')->nullable();
            $table->integer('progreso_porcentaje')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('proxima_cita_semanas')->nullable();
            $table->text('indicaciones_paciente')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles_ortodoncia');
    }
};
