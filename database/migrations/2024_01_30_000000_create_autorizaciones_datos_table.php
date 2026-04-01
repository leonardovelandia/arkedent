<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autorizaciones_datos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_autorizacion', 20)->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha_autorizacion');
            $table->boolean('acepta_almacenamiento')->default(true);
            $table->boolean('acepta_contacto_whatsapp')->default(false);
            $table->boolean('acepta_contacto_email')->default(false);
            $table->boolean('acepta_contacto_llamada')->default(false);
            $table->boolean('acepta_recordatorios')->default(false);
            $table->boolean('acepta_compartir_entidades')->default(false);
            $table->boolean('firmado')->default(false);
            $table->longText('firma_data')->nullable();
            $table->datetime('fecha_firma')->nullable();
            $table->string('ip_firma', 45)->nullable();
            $table->enum('metodo_firma', ['digital', 'impresa'])->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autorizaciones_datos');
    }
};
