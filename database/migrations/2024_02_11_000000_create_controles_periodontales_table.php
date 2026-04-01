<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('controles_periodontales', function (Blueprint $table) {
            $table->id();
            $table->string('numero_control', 20)->unique();
            $table->foreignId('ficha_periodontal_id')->constrained('fichas_periodontales')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->date('fecha_control');
            $table->integer('numero_sesion');
            $table->enum('tipo_sesion', ['raspado_alisado','curetaje','cirugia_periodontal','mantenimiento','reevaluacion','otro']);
            $table->json('zonas_tratadas')->nullable();
            $table->json('sondaje_control')->nullable();
            $table->decimal('indice_placa_control', 5, 2)->nullable();
            $table->decimal('indice_gingival_control', 5, 2)->nullable();
            $table->string('anestesia_utilizada', 100)->nullable();
            $table->text('instrumentos_utilizados')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('indicaciones_paciente')->nullable();
            $table->integer('proxima_cita_semanas')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('controles_periodontales'); }
};
