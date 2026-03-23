<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_presupuesto', 20)->unique()->nullable();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha_generacion');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['borrador', 'enviado', 'aprobado', 'rechazado', 'vencido'])->default('borrador');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_valor', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('condiciones_pago')->nullable();
            $table->integer('validez_dias')->default(30);
            $table->text('observaciones')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->datetime('fecha_aprobacion')->nullable();
            $table->boolean('firmado')->default(false);
            $table->longText('firma_data')->nullable();
            $table->string('ip_firma', 45)->nullable();
            $table->foreignId('tratamiento_id')->nullable()->constrained('tratamientos')->onDelete('set null');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
