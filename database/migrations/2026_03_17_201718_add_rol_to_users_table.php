<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['doctor', 'asistente', 'administrador'])->default('doctor')->after('email');
            $table->boolean('activo')->default(true)->after('rol');
        });

        // Asignar roles a usuarios existentes
        DB::table('users')->where('email', 'admin@consultorio.com')->update(['rol' => 'administrador']);
        DB::table('users')->where('email', 'tatiana@consultorio.com')->update(['rol' => 'doctor']);
        DB::table('users')->whereNotIn('email', ['admin@consultorio.com', 'tatiana@consultorio.com'])->update(['rol' => 'doctor']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'activo']);
        });
    }
};
