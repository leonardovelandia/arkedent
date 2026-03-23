<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_recibo', 20)->unique();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('tratamiento_id')->nullable()->constrained('tratamientos')->onDelete('set null');
            $table->foreignId('user_id')->constrained();
            $table->string('concepto', 255);
            $table->decimal('valor', 12, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta_credito', 'tarjeta_debito', 'cheque', 'otro']);
            $table->string('referencia_pago', 100)->nullable();
            $table->date('fecha_pago');
            $table->text('observaciones')->nullable();
            $table->boolean('anulado')->default(false);
            $table->string('motivo_anulacion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
