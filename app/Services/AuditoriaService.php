<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditoriaService
{
    public static function registrar(
        string  $accion,
        string  $modulo,
        ?int    $registroId  = null,
        ?string $descripcion = null
    ): void {
        try {
            DB::table('logs_auditoria')->insert([
                'user_id'     => auth()->id(),
                'user_nombre' => auth()->user()?->name ?? 'Sistema',
                'accion'      => $accion,
                'modulo'      => $modulo,
                'registro_id' => $registroId,
                'descripcion' => $descripcion,
                'ip'          => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'created_at'  => now(),
            ]);

            Log::channel('auditoria')->info("{$accion} en {$modulo}", [
                'user_id'  => auth()->id(),
                'modulo'   => $modulo,
                'registro' => $registroId,
                'ip'       => request()->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error en auditoría: ' . $e->getMessage());
        }
    }

    public static function verPaciente(int $id): void
    {
        self::registrar('VER', 'pacientes', $id);
    }

    public static function crearEvolucion(int $id): void
    {
        self::registrar('CREAR', 'evoluciones', $id);
    }

    public static function firmarDocumento(string $tipo, int $id): void
    {
        self::registrar('FIRMAR', $tipo, $id, 'Documento firmado digitalmente');
    }

    public static function login(): void
    {
        self::registrar('LOGIN', 'autenticacion', auth()->id());
    }

    public static function logout(): void
    {
        self::registrar('LOGOUT', 'autenticacion', auth()->id());
    }

    public static function exportar(string $modulo): void
    {
        self::registrar('EXPORTAR', $modulo);
    }

    public static function crear(string $modulo, int $id, ?string $desc = null): void
    {
        self::registrar('CREAR', $modulo, $id, $desc);
    }

    public static function editar(string $modulo, int $id, ?string $desc = null): void
    {
        self::registrar('EDITAR', $modulo, $id, $desc);
    }

    public static function eliminar(string $modulo, int $id, ?string $desc = null): void
    {
        self::registrar('ELIMINAR', $modulo, $id, $desc);
    }
}
