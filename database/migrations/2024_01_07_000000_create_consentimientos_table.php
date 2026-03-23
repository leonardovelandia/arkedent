<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consentimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('plantilla_id')->nullable()->constrained('plantillas_consentimiento')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users');
            $table->string('nombre', 150);
            $table->longText('contenido');
            $table->date('fecha_generacion');
            $table->datetime('fecha_firma')->nullable();
            $table->boolean('firmado')->default(false);
            $table->string('firma_path', 255)->nullable();
            $table->longText('firma_data')->nullable();
            $table->string('ip_firma', 45)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimientos');
    }
};
