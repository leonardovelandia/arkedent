<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('tema', 30)->default('morado-elegante')->after('logo_path');
            $table->string('fuente_principal', 50)->default('DM Sans')->after('tema');
            $table->string('fuente_titulos', 50)->default('Playfair Display')->after('fuente_principal');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['tema', 'fuente_principal', 'fuente_titulos']);
        });
    }
};