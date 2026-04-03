<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Traits\FormateaCampos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PacienteController extends Controller
{
    use FormateaCampos;
    // ── Listado con búsqueda, filtros y paginación dinámica ──
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50])
            ? (int) $request->input('per_page', 10)
            : 10;

        $query = Paciente::query()->orderBy('apellido');

        if ($buscar = trim($request->input('buscar', ''))) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre',           'like', "%{$buscar}%")
                  ->orWhere('apellido',        'like', "%{$buscar}%")
                  ->orWhere('numero_documento','like', "%{$buscar}%")
                  ->orWhere('telefono',        'like', "%{$buscar}%")
                  ->orWhere('email',           'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->input('estado') === 'activo');
        }

        $pacientes = $query->with('autorizacionDatos')->paginate($perPage)->withQueryString();

        return view('pacientes.index', compact('pacientes'));
    }

    // ── Formulario de creación ────────────────────────────────
    public function create()
    {
        return view('pacientes.create');
    }

    // ── Guardar nuevo paciente ────────────────────────────────
    public function store(Request $request)
    {
        $validado = $request->validate([
            'nombre'              => 'required|string|max:100',
            'apellido'            => 'required|string|max:100',
            'tipo_documento'      => 'required|in:CC,TI,CE,PA,RC',
            'numero_documento'    => 'required|string|max:20|unique:pacientes,numero_documento',
            'fecha_nacimiento'    => 'required|date|before:today',
            'genero'              => 'required|in:masculino,femenino,otro',
            'telefono'            => 'required|string|max:20',
            'telefono_emergencia' => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:120',
            'direccion'           => 'nullable|string|max:255',
            'ciudad'              => 'nullable|string|max:100',
            'ocupacion'           => 'nullable|string|max:100',
            'nombre_acudiente'    => 'nullable|string|max:150',
            'observaciones'       => 'nullable|string',
            'foto'                => 'nullable|image|max:2048',
        ]);

        $datos = $this->formatearDatos($validado);

        if ($request->hasFile('foto')) {
            $datos['foto_path'] = $request->file('foto')->store('pacientes', 'public');
        } elseif ($request->filled('foto_base64')) {
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('foto_base64'));
            $imageData  = base64_decode($base64Data);
            $filename   = 'pacientes/cam_' . uniqid() . '.jpg';
            \Storage::disk('public')->put($filename, $imageData);
            $datos['foto_path'] = $filename;
        }

        $paciente = Paciente::create($datos);

        if ($request->boolean('crear_autorizacion')) {
            return redirect()->route('autorizacion.create', ['paciente_id' => $paciente->id])
                ->with('exito', 'Paciente creado. Completa la autorización de datos personales.');
        }

        return redirect()->route('pacientes.show', $paciente)
                         ->with('exito', 'Paciente registrado correctamente.');
    }

    // ── Ficha completa del paciente ───────────────────────────
    public function show(string $id)
    {
        $paciente = Paciente::findOrFail($id);

        return view('pacientes.show', compact('paciente'));
    }

    // ── Formulario de edición ─────────────────────────────────
    public function edit(string $id)
    {
        $paciente = Paciente::findOrFail($id);

        return view('pacientes.edit', compact('paciente'));
    }

    // ── Actualizar paciente ───────────────────────────────────
    public function update(Request $request, string $id)
    {
        $paciente = Paciente::findOrFail($id);

        $validado = $request->validate([
            'nombre'              => 'required|string|max:100',
            'apellido'            => 'required|string|max:100',
            'tipo_documento'      => 'required|in:CC,TI,CE,PA,RC',
            'numero_documento'    => 'required|string|max:20|unique:pacientes,numero_documento,' . $paciente->id,
            'fecha_nacimiento'    => 'required|date|before:today',
            'genero'              => 'required|in:masculino,femenino,otro',
            'telefono'            => 'required|string|max:20',
            'telefono_emergencia' => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:120',
            'direccion'           => 'nullable|string|max:255',
            'ciudad'              => 'nullable|string|max:100',
            'ocupacion'           => 'nullable|string|max:100',
            'nombre_acudiente'    => 'nullable|string|max:150',
            'observaciones'       => 'nullable|string',
            'foto'                => 'nullable|image|max:2048',
        ]);

        $datos = $this->formatearDatos($validado);

        if ($request->hasFile('foto')) {
            if ($paciente->foto_path) Storage::disk('public')->delete($paciente->foto_path);
            $datos['foto_path'] = $request->file('foto')->store('pacientes', 'public');
        } elseif ($request->filled('foto_base64')) {
            if ($paciente->foto_path) Storage::disk('public')->delete($paciente->foto_path);
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('foto_base64'));
            $imageData  = base64_decode($base64Data);
            $filename   = 'pacientes/cam_' . uniqid() . '.jpg';
            \Storage::disk('public')->put($filename, $imageData);
            $datos['foto_path'] = $filename;
        }

        $paciente->update($datos);

        return redirect()->route('pacientes.show', $paciente)
                         ->with('exito', 'Paciente actualizado correctamente.');
    }

    // ── Desactivar paciente ───────────────────────────────────
    public function destroy(string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update(['activo' => false]);

        return redirect()->route('pacientes.index')
                         ->with('exito', 'Paciente desactivado correctamente.');
    }

    // ── Activar paciente ──────────────────────────────────────
    public function activar(string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update(['activo' => true]);

        return redirect()->route('pacientes.index')
                         ->with('exito', 'Paciente activado correctamente.');
    }

    // ── Eliminar paciente definitivamente ─────────────────────
    public function eliminar(string $id)
    {
        $paciente = Paciente::findOrFail($id);

        if ($paciente->foto_path) {
            Storage::disk('public')->delete($paciente->foto_path);
        }

        $paciente->delete();

        return redirect()->route('pacientes.index')
                         ->with('exito', 'Paciente eliminado permanentemente.');
    }
}
