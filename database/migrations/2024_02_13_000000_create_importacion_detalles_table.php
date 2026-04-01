<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('importacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacion_id')->constrained('importaciones')->onDelete('cascade');
            $table->integer('fila_numero');
            $table->json('datos_originales');
            $table->json('datos_transformados')->nullable();
            $table->string('modelo', 100)->nullable();
            $table->integer('registro_id')->nullable();
            $table->enum('estado', ['importado','omitido','duplicado','error']);
            $table->string('mensaje', 255)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('importacion_detalles'); }
};
