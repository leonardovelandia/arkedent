<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('importaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_importacion', 20)->unique();
            $table->foreignId('user_id')->constrained();
            $table->enum('fuente', ['dentox','odontosof','dentalpro','excel_generico','csv_generico','sql_dump']);
            $table->enum('tipo_datos', ['pacientes','historia_clinica','citas','tratamientos','pagos','evoluciones','todo']);
            $table->string('archivo_nombre', 255);
            $table->string('archivo_path', 255);
            $table->integer('total_registros')->default(0);
            $table->integer('registros_importados')->default(0);
            $table->integer('registros_omitidos')->default(0);
            $table->integer('registros_error')->default(0);
            $table->integer('registros_duplicados')->default(0);
            $table->enum('estado', ['pendiente','procesando','completado','error','revertido'])->default('pendiente');
            $table->json('log_importacion')->nullable();
            $table->json('errores')->nullable();
            $table->boolean('puede_revertir')->default(true);
            $table->datetime('fecha_importacion')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('importaciones'); }
};
