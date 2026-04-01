<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('logs_auditoria')->orderByDesc('created_at');

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('usuario')) {
            $query->where('user_nombre', 'like', '%' . $request->usuario . '%');
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        $logs    = $query->paginate(50)->withQueryString();
        $modulos = DB::table('logs_auditoria')->distinct()->orderBy('modulo')->pluck('modulo');
        $acciones = DB::table('logs_auditoria')->distinct()->orderBy('accion')->pluck('accion');

        return view('auditoria.index', compact('logs', 'modulos', 'acciones'));
    }

    public function exportar(Request $request)
    {
        $query = DB::table('logs_auditoria')->orderByDesc('created_at');

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('usuario')) {
            $query->where('user_nombre', 'like', '%' . $request->usuario . '%');
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        $logs = $query->limit(10000)->get();

        $csv  = "ID,Fecha,Usuario,Acción,Módulo,Registro ID,Descripción,IP\n";
        foreach ($logs as $log) {
            $csv .= implode(',', [
                $log->id,
                '"' . $log->created_at . '"',
                '"' . str_replace('"', '""', $log->user_nombre ?? '') . '"',
                '"' . $log->accion . '"',
                '"' . $log->modulo . '"',
                $log->registro_id ?? '',
                '"' . str_replace('"', '""', $log->descripcion ?? '') . '"',
                '"' . ($log->ip ?? '') . '"',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="auditoria_' . now()->format('Ymd_His') . '.csv"',
        ]);
    }
}
