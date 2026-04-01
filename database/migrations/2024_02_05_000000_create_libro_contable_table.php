<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libro_contable', function (Blueprint $table) {
            $table->id();
            $table->string('numero_asiento', 20)->unique();
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->enum('origen', [
                'pago_paciente',
                'egreso_manual',
                'compra_proveedor',
                'gasto_laboratorio',
                'ajuste_manual',
            ]);
            $table->integer('origen_id')->nullable();
            $table->string('origen_tipo', 100)->nullable();
            $table->string('concepto', 255);
            $table->text('descripcion')->nullable();
            $table->decimal('valor', 12, 2);
            $table->date('fecha_movimiento');
            $table->string('metodo_pago', 50)->nullable();
            $table->string('referencia', 100)->nullable();
            $table->string('categoria', 100)->nullable();
            $table->boolean('excluido')->default(false);
            $table->string('motivo_exclusion', 255)->nullable();
            $table->foreignId('user_id')->constrained();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libro_contable');
    }
};
