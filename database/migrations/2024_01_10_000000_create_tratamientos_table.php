<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->onDelete('set null');
            $table->foreignId('user_id')->constrained();
            $table->string('nombre', 255);
            $table->decimal('valor_total', 12, 2);
            $table->decimal('saldo_pendiente', 12, 2);
            $table->enum('estado', ['activo', 'completado', 'cancelado'])->default('activo');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
