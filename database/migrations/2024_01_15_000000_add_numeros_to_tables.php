<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->string('numero_historia', 20)->nullable()->unique()->after('id');
        });

        Schema::table('evoluciones', function (Blueprint $table) {
            $table->string('numero_evolucion', 20)->nullable()->unique()->after('id');
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->string('numero_cita', 20)->nullable()->unique()->after('id');
        });

        Schema::table('consentimientos', function (Blueprint $table) {
            $table->string('numero_consentimiento', 20)->nullable()->unique()->after('id');
        });

        Schema::table('correcciones_historia', function (Blueprint $table) {
            $table->string('numero_correccion', 20)->nullable()->unique()->after('id');
        });

        Schema::table('correcciones_evolucion', function (Blueprint $table) {
            $table->string('numero_correccion', 20)->nullable()->unique()->after('id');
        });

        Schema::table('tratamientos', function (Blueprint $table) {
            $table->string('numero_tratamiento', 20)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('historias_clinicas',   fn ($t) => $t->dropColumn('numero_historia'));
        Schema::table('evoluciones',          fn ($t) => $t->dropColumn('numero_evolucion'));
        Schema::table('citas',                fn ($t) => $t->dropColumn('numero_cita'));
        Schema::table('consentimientos',      fn ($t) => $t->dropColumn('numero_consentimiento'));
        Schema::table('correcciones_historia',fn ($t) => $t->dropColumn('numero_correccion'));
        Schema::table('correcciones_evolucion',fn ($t) => $t->dropColumn('numero_correccion'));
        Schema::table('tratamientos',         fn ($t) => $t->dropColumn('numero_tratamiento'));
    }
};
