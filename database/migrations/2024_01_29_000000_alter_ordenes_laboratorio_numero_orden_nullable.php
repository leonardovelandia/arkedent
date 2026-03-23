<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_laboratorio', function (Blueprint $table) {
            $table->dropUnique('ordenes_laboratorio_numero_orden_unique');
            $table->string('numero_orden', 20)->nullable()->change();
            $table->unique('numero_orden');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes_laboratorio', function (Blueprint $table) {
            $table->string('numero_orden', 20)->nullable(false)->change();
        });
    }
};
