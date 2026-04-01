<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retencion_ortodoncia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ficha_ortodontica_id')->constrained('fichas_ortodonticas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('fecha_retiro_brackets')->nullable();
            $table->enum('tipo_retenedor_superior', ['fijo_alambre', 'removible_hawley', 'alineador_retencion', 'ninguno'])->nullable();
            $table->enum('tipo_retenedor_inferior', ['fijo_alambre', 'removible_hawley', 'alineador_retencion', 'ninguno'])->nullable();
            $table->date('fecha_entrega_retenedor')->nullable();
            $table->text('instrucciones_uso')->nullable();
            $table->integer('duracion_retencion_meses')->nullable();
            $table->json('controles_retencion')->nullable();
            $table->enum('estado', ['pendiente', 'activa', 'finalizada'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retencion_ortodoncia');
    }
};
