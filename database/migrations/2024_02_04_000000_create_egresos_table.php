<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_egreso', 20)->unique();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_egreso')->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('concepto', 255);
            $table->text('descripcion')->nullable();
            $table->decimal('valor', 12, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta_credito', 'tarjeta_debito', 'cheque', 'otro']);
            $table->date('fecha_egreso');
            $table->string('numero_comprobante', 100)->nullable();
            $table->string('comprobante_path', 255)->nullable();
            $table->boolean('es_recurrente')->default(false);
            $table->enum('frecuencia_recurrente', ['diario', 'semanal', 'quincenal', 'mensual', 'bimestral', 'trimestral', 'semestral', 'anual'])->nullable();
            $table->integer('dia_recurrente')->nullable();
            $table->date('proxima_fecha')->nullable();
            $table->boolean('anulado')->default(false);
            $table->string('motivo_anulacion', 255)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};
