<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ModulosHelper;

class VerificarModulo
{
    public function handle(Request $request, Closure $next, string $modulo): mixed
    {
        if (!ModulosHelper::activo($modulo)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'Módulo no disponible',
                    'modulo'  => $modulo,
                    'mensaje' => 'Este módulo no está disponible en el plan actual.',
                ], 403);
            }

            $nombre = config("modulos.catalogo.{$modulo}.nombre", $modulo);

            return redirect()
                ->route('dashboard')
                ->with('aviso', "El módulo «{$nombre}» no está disponible en el plan actual. Contacta al administrador del sistema para activarlo.");
        }

        return $next($request);
    }
}
