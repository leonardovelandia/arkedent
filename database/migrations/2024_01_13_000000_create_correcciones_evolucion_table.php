<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correcciones_evolucion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evolucion_id')->constrained('evoluciones')->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('campo_corregido', 100);
            $table->text('valor_anterior');
            $table->text('valor_nuevo');
            $table->text('motivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correcciones_evolucion');
    }
};
