<?php

namespace App\Http\Controllers;

use App\Models\FichaOrtodoncia;
use App\Models\RetencionOrtodoncia;
use App\Models\User;
use Illuminate\Http\Request;

class RetencionOrtodonciaController extends Controller
{
    public function create(Request $request)
    {
        $fichaId = $request->route('ortodoncia') ?? $request->ficha_ortodontica_id;
        $ficha   = FichaOrtodoncia::with('paciente')->findOrFail($fichaId);
        $ortodoncistas = User::all();

        return view('ortodoncia.retencion.create', compact('ficha', 'ortodoncistas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ficha_ortodontica_id'    => 'required|exists:fichas_ortodonticas,id',
            'paciente_id'             => 'required|exists:pacientes,id',
            'user_id'                 => 'required|exists:users,id',
            'fecha_retiro_brackets'   => 'nullable|date',
            'tipo_retenedor_superior' => 'nullable|in:fijo_alambre,removible_hawley,alineador_retencion,ninguno',
            'tipo_retenedor_inferior' => 'nullable|in:fijo_alambre,removible_hawley,alineador_retencion,ninguno',
            'fecha_entrega_retenedor' => 'nullable|date',
            'instrucciones_uso'       => 'nullable|string',
            'duracion_retencion_meses'=> 'nullable|integer|min:1',
            'notas'                   => 'nullable|string',
        ]);

        $retencion = RetencionOrtodoncia::create($validated);

        // Cambiar estado de la ficha a retención
        FichaOrtodoncia::where('id', $validated['ficha_ortodontica_id'])
            ->update(['estado' => 'retencion']);

        return redirect()->route('ortodoncia.show', $validated['ficha_ortodontica_id'])
            ->with('exito', 'Fase de retención registrada correctamente.');
    }

    public function show($id)
    {
        $retencion = RetencionOrtodoncia::with(['fichaOrtodoncia.paciente', 'ortodoncista'])->findOrFail($id);
        return view('ortodoncia.retencion.show', compact('retencion'));
    }

    public function edit($id)
    {
        $retencion     = RetencionOrtodoncia::with('fichaOrtodoncia')->findOrFail($id);
        $ortodoncistas = User::all();
        return view('ortodoncia.retencion.edit', compact('retencion', 'ortodoncistas'));
    }

    public function update(Request $request, $id)
    {
        $retencion = RetencionOrtodoncia::findOrFail($id);

        $validated = $request->validate([
            'user_id'                 => 'required|exists:users,id',
            'fecha_retiro_brackets'   => 'nullable|date',
            'tipo_retenedor_superior' => 'nullable|in:fijo_alambre,removible_hawley,alineador_retencion,ninguno',
            'tipo_retenedor_inferior' => 'nullable|in:fijo_alambre,removible_hawley,alineador_retencion,ninguno',
            'fecha_entrega_retenedor' => 'nullable|date',
            'instrucciones_uso'       => 'nullable|string',
            'duracion_retencion_meses'=> 'nullable|integer|min:1',
            'estado'                  => 'required|in:pendiente,activa,finalizada',
            'notas'                   => 'nullable|string',
        ]);

        $retencion->update($validated);

        return redirect()->route('ortodoncia.show', $retencion->ficha_ortodontica_id)
            ->with('exito', 'Retención actualizada correctamente.');
    }

    public function destroy($id)
    {
        $retencion = RetencionOrtodoncia::findOrFail($id);
        $fichaId   = $retencion->ficha_ortodontica_id;
        $retencion->delete();

        return redirect()->route('ortodoncia.show', $fichaId)
            ->with('exito', 'Retención eliminada.');
    }
}
