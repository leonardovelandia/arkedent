<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla de configuración del consultorio
 * 
 * Esta tabla almacena los datos del consultorio que aparecen
 * en vistas, PDFs, reportes y encabezados. NUNCA hardcodear
 * el nombre del consultorio en el código.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();

            // Datos del consultorio
            $table->string('nombre_consultorio', 150)->default('Consultorio Odontológico');
            $table->string('slogan', 255)->nullable();
            $table->string('nit', 30)->nullable();
            $table->string('registro_medico', 60)->nullable();

            // Contacto
            $table->string('telefono', 30)->nullable();
            $table->string('telefono_whatsapp', 30)->nullable();
            $table->string('email', 120)->nullable();

            // Ubicación
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->nullable()->default('Colombia');
            $table->string('pais', 80)->nullable()->default('Colombia');

            // Imagen / Logo
            $table->string('logo_path', 255)->nullable();

            // Configuración de citas
            $table->integer('duracion_cita_minutos')->default(30);
            $table->time('hora_apertura')->default('08:00:00');
            $table->time('hora_cierre')->default('18:00:00');
            $table->json('dias_laborales')->nullable(); // [1,2,3,4,5] = L-V

            // Moneda y formato
            $table->string('moneda', 10)->default('COP');
            $table->string('simbolo_moneda', 5)->default('$');

            // Notificaciones
            $table->boolean('recordatorios_activos')->default(false);
            $table->integer('horas_anticipacion_recordatorio')->default(24);

            // Metadatos
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};