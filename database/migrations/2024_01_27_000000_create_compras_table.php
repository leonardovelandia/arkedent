<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('numero_compra', 20)->unique();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha_compra');
            $table->string('numero_factura', 50)->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('descuento_valor', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta_credito', 'tarjeta_debito', 'cheque', 'credito', 'otro']);
            $table->enum('estado', ['pendiente', 'pagada', 'cancelada'])->default('pendiente');
            $table->date('fecha_vencimiento')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
