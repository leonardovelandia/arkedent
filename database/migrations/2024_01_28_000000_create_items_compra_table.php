<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->onDelete('cascade');
            $table->foreignId('material_id')->nullable()->constrained('materiales')->onDelete('set null');
            $table->string('descripcion', 255);
            $table->decimal('cantidad', 10, 2);
            $table->string('unidad_medida', 30);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->boolean('actualizo_inventario')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items_compra');
    }
};
