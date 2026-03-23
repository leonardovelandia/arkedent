<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_laboratorio', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 20)->unique();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('laboratorio_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('evolucion_id')->nullable()->constrained('evoluciones')->onDelete('set null');
            $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');
            $table->string('tipo_trabajo', 150);
            $table->text('descripcion');
            $table->string('dientes', 100)->nullable();
            $table->string('color_diente', 50)->nullable();
            $table->string('material', 100)->nullable();
            $table->date('fecha_envio')->nullable();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_recepcion')->nullable();
            $table->date('fecha_instalacion')->nullable();
            $table->enum('estado', ['pendiente', 'enviado', 'en_proceso', 'recibido', 'instalado', 'cancelado'])->default('pendiente');
            $table->decimal('precio_laboratorio', 12, 2)->nullable();
            $table->text('observaciones_envio')->nullable();
            $table->text('observaciones_recepcion')->nullable();
            $table->enum('calidad_recibida', ['excelente', 'buena', 'regular', 'mala'])->nullable();
            $table->boolean('requiere_ajuste')->default(false);
            $table->string('motivo_cancelacion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_laboratorio');
    }
};
