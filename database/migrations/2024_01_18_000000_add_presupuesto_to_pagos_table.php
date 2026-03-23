<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('presupuesto_id')->nullable()->constrained('presupuestos')->onDelete('set null')->after('tratamiento_id');
            $table->boolean('es_pago_libre')->default(false)->after('presupuesto_id');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['presupuesto_id']);
            $table->dropColumn(['presupuesto_id', 'es_pago_libre']);
        });
    }
};
