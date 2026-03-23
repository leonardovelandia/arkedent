<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use App\Traits\FormateaCampos;
use Illuminate\Http\Request;

class GestionLaboratorioController extends Controller
{
    use FormateaCampos;

    public function index()
    {
        $laboratorios = Laboratorio::withCount(['ordenes as ordenes_activas_count' => function ($q) {
            $q->where('activo', true)->whereNotIn('estado', ['instalado', 'cancelado']);
        }])->orderBy('nombre')->get();

        return view('laboratorio.laboratorios.index', compact('laboratorios'));
    }

    public function create()
    {
        return view('laboratorio.laboratorios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'               => 'required|string|max:150',
            'contacto'             => 'nullable|string|max:150',
            'telefono'             => 'nullable|string|max:30',
            'whatsapp'             => 'nullable|string|max:30',
            'email'                => 'nullable|email|max:120',
            'direccion'            => 'nullable|string|max:255',
            'ciudad'               => 'nullable|string|max:100',
            'especialidades'       => 'nullable|array',
            'especialidades.*'     => 'string',
            'tiempo_entrega_dias'  => 'nullable|integer|min:1',
            'notas'                => 'nullable|string',
        ]);

        $validated = $this->formatearDatos($validated);
        Laboratorio::create($validated);

        return redirect()->route('gestion-laboratorios.index')
            ->with('exito', 'Laboratorio registrado correctamente.');
    }

    public function show(Laboratorio $gestionLaboratorio)
    {
        $laboratorio = $gestionLaboratorio->load('ordenes.paciente');
        return view('laboratorio.laboratorios.show', compact('laboratorio'));
    }

    public function edit(Laboratorio $gestionLaboratorio)
    {
        $laboratorio = $gestionLaboratorio;
        return view('laboratorio.laboratorios.edit', compact('laboratorio'));
    }

    public function update(Request $request, Laboratorio $gestionLaboratorio)
    {
        $validated = $request->validate([
            'nombre'               => 'required|string|max:150',
            'contacto'             => 'nullable|string|max:150',
            'telefono'             => 'nullable|string|max:30',
            'whatsapp'             => 'nullable|string|max:30',
            'email'                => 'nullable|email|max:120',
            'direccion'            => 'nullable|string|max:255',
            'ciudad'               => 'nullable|string|max:100',
            'especialidades'       => 'nullable|array',
            'especialidades.*'     => 'string',
            'tiempo_entrega_dias'  => 'nullable|integer|min:1',
            'notas'                => 'nullable|string',
            'activo'               => 'nullable|boolean',
        ]);

        $validated = $this->formatearDatos($validated);
        $validated['activo'] = $request->boolean('activo', true);

        $gestionLaboratorio->update($validated);

        return redirect()->route('gestion-laboratorios.index')
            ->with('exito', 'Laboratorio actualizado correctamente.');
    }

    public function destroy(Laboratorio $gestionLaboratorio)
    {
        $gestionLaboratorio->update(['activo' => false]);

        return redirect()->route('gestion-laboratorios.index')
            ->with('exito', 'Laboratorio desactivado.');
    }
}
