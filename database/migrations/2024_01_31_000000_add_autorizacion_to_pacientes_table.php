<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->boolean('autorizacion_datos')->default(false)->after('activo');
            $table->datetime('fecha_autorizacion_datos')->nullable()->after('autorizacion_datos');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['autorizacion_datos', 'fecha_autorizacion_datos']);
        });
    }
};
