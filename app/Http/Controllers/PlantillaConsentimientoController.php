<?php

namespace App\Http\Controllers;

use App\Models\PlantillaConsentimiento;
use Illuminate\Http\Request;

class PlantillaConsentimientoController extends Controller
{
    public function index()
    {
        $plantillas = PlantillaConsentimiento::orderBy('nombre')->get();
        return view('consentimientos.plantillas.index', compact('plantillas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:150',
            'tipo'      => 'nullable|string|max:100',
            'contenido' => 'required|string',
        ]);

        $data['activo'] = true;
        PlantillaConsentimiento::create($data);

        return redirect()->route('plantillas-consentimiento.index')
                         ->with('exito', 'Plantilla creada correctamente.');
    }

    public function edit(PlantillaConsentimiento $plantillasConsentimiento)
    {
        return view('consentimientos.plantillas.edit', [
            'plantilla' => $plantillasConsentimiento,
        ]);
    }

    public function update(Request $request, PlantillaConsentimiento $plantillasConsentimiento)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:150',
            'tipo'      => 'nullable|string|max:100',
            'contenido' => 'required|string',
            'activo'    => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $plantillasConsentimiento->update($data);

        return redirect()->route('plantillas-consentimiento.index')
                         ->with('exito', 'Plantilla actualizada correctamente.');
    }

    public function destroy(PlantillaConsentimiento $plantillasConsentimiento)
    {
        $usos = $plantillasConsentimiento->consentimientos()->count();
        if ($usos > 0) {
            // Desactivar en lugar de eliminar si tiene consentimientos asociados
            $plantillasConsentimiento->update(['activo' => false]);
            return redirect()->route('plantillas-consentimiento.index')
                             ->with('exito', 'Plantilla desactivada (tiene ' . $usos . ' consentimiento(s) asociado(s)).');
        }

        $plantillasConsentimiento->delete();
        return redirect()->route('plantillas-consentimiento.index')
                         ->with('exito', 'Plantilla eliminada correctamente.');
    }
}
