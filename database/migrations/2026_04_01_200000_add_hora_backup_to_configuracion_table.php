<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('hora_backup', 5)->default('02:00')->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn('hora_backup');
        });
    }
};
