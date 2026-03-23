<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_historia', 20)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->enum('tipo_documento', ['CC', 'TI', 'CE', 'PA', 'RC']);
            $table->string('numero_documento', 20)->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['masculino', 'femenino', 'otro']);
            $table->string('telefono', 20);
            $table->string('telefono_emergencia', 20)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('ocupacion', 100)->nullable();
            $table->string('nombre_acudiente', 150)->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
