<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materiales')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->decimal('cantidad', 10, 2);
            $table->decimal('stock_anterior', 10, 2);
            $table->decimal('stock_posterior', 10, 2);
            $table->string('concepto', 255);
            $table->foreignId('evolucion_id')->nullable()->constrained('evoluciones')->onDelete('set null');
            $table->decimal('precio_unitario', 12, 2)->nullable();
            $table->string('proveedor', 150)->nullable();
            $table->string('numero_factura', 50)->nullable();
            $table->date('fecha_movimiento');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
