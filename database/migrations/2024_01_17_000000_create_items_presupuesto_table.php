<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presupuesto_id')->constrained('presupuestos')->onDelete('cascade');
            $table->integer('numero_item');
            $table->string('procedimiento', 255);
            $table->string('diente', 20)->nullable();
            $table->string('cara', 50)->nullable();
            $table->integer('cantidad')->default(1);
            $table->decimal('valor_unitario', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->boolean('completado')->default(false);
            $table->string('notas', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items_presupuesto');
    }
};
