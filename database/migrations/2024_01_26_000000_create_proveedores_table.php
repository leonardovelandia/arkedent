<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->nullable();
            $table->string('nombre', 150);
            $table->string('nit', 30)->nullable();
            $table->string('contacto', 150)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->json('categorias')->nullable();
            $table->integer('tiempo_entrega_dias')->nullable();
            $table->string('condiciones_pago', 255)->nullable();
            $table->decimal('calificacion', 3, 1)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
