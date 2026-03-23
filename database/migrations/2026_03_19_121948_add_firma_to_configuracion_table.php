<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('firma_path', 255)->nullable()->after('logo_path');
            $table->string('firma_nombre_doctor', 120)->nullable()->after('firma_path');
            $table->string('firma_cargo', 80)->nullable()->after('firma_nombre_doctor');
            $table->string('firma_registro', 60)->nullable()->after('firma_cargo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['firma_path', 'firma_nombre_doctor', 'firma_cargo', 'firma_registro']);
        });
    }
};
