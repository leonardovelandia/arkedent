<?php

namespace App\Http\Controllers;

use App\Models\ImagenClinica;
use App\Models\Paciente;
use App\Models\HistoriaClinica;
use App\Models\Evolucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenClinicaController extends Controller
{
    public function index(Request $request)
    {
        $buscar     = $request->input('buscar');
        $pacienteId = $request->input('paciente_id');
        $tipo       = $request->input('tipo');
        $desde      = $request->input('desde');
        $hasta      = $request->input('hasta');

        $imagenes = ImagenClinica::with(['paciente', 'autor'])
            ->activas()
            ->when($pacienteId, fn($q) => $q->where('paciente_id', $pacienteId))
            ->when(!$pacienteId && $buscar, function ($q) use ($buscar) {
                $q->whereHas('paciente', fn($p) =>
                    $p->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('apellido', 'like', "%{$buscar}%")
                      ->orWhere('numero_documento', 'like', "%{$buscar}%")
                );
            })
            ->when($tipo, fn($q) => $q->where('tipo', $tipo))
            ->when($desde, fn($q) => $q->whereDate('fecha_toma', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('fecha_toma', '<=', $hasta))
            ->orderBy('fecha_toma', 'desc')
            ->paginate(24)
            ->withQueryString();

        $pacientes = Paciente::activos()->orderBy('apellido')->orderBy('nombre')->get();

        $tipos = [
            'fotografia_intraoral'   => 'Fotografía Intraoral',
            'fotografia_extraoral'   => 'Fotografía Extraoral',
            'radiografia_periapical' => 'Radiografía Periapical',
            'radiografia_panoramica' => 'Radiografía Panorámica',
            'radiografia_bitewing'   => 'Radiografía Bitewing',
            'foto_antes'             => 'Foto Antes del Tratamiento',
            'foto_durante'           => 'Foto Durante el Tratamiento',
            'foto_despues'           => 'Foto Después del Tratamiento',
            'foto_sonrisa'           => 'Foto de Sonrisa',
            'otra'                   => 'Otra',
        ];

        if ($request->ajax()) {
            return view('imagenes._grid', compact('imagenes'));
        }

        return view('imagenes.index', compact('imagenes', 'buscar', 'pacienteId', 'tipo', 'desde', 'hasta', 'tipos', 'pacientes'));
    }

    public function create(Request $request)
    {
        $pacienteId  = $request->input('paciente_id');
        $evolucionId = $request->input('evolucion_id');
        $paciente    = null;
        $historia    = null;
        $evolucion   = null;

        if ($pacienteId) {
            $paciente  = Paciente::findOrFail($pacienteId);
            $historia  = $paciente->historiaClinica;
        }

        if ($evolucionId) {
            $evolucion = Evolucion::find($evolucionId);
            if ($evolucion && !$paciente) {
                $paciente = $evolucion->paciente;
                $historia = $paciente->historiaClinica;
            }
        }

        $pacientes = Paciente::activos()->orderBy('apellido')->get();

        $tipos = [
            'fotografia_intraoral'   => 'Fotografía Intraoral',
            'fotografia_extraoral'   => 'Fotografía Extraoral',
            'radiografia_periapical' => 'Radiografía Periapical',
            'radiografia_panoramica' => 'Radiografía Panorámica',
            'radiografia_bitewing'   => 'Radiografía Bitewing',
            'foto_antes'             => 'Foto Antes del Tratamiento',
            'foto_durante'           => 'Foto Durante el Tratamiento',
            'foto_despues'           => 'Foto Después del Tratamiento',
            'foto_sonrisa'           => 'Foto de Sonrisa',
            'otra'                   => 'Otra',
        ];

        return view('imagenes.create', compact('paciente', 'historia', 'evolucion', 'pacientes', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id'       => 'required|exists:pacientes,id',
            'imagenes'          => 'required|array|min:1|max:10',
            'imagenes.*'        => 'required|file|mimes:jpg,jpeg,png,webp,gif,bmp|max:10240',
            'tipo'              => 'required|string',
            'titulo'            => 'required|string|max:150',
            'descripcion'       => 'nullable|string',
            'fecha_toma'        => 'required|date',
            'diente'            => 'nullable|string|max:20',
            'evolucion_id'      => 'nullable|exists:evoluciones,id',
            'es_comparativo'    => 'boolean',
            'orden_comparativo' => 'nullable|in:antes,durante,despues',
        ]);

        $archivos        = $request->file('imagenes');
        $imagenesCreadas = [];

        $paciente = Paciente::find($request->paciente_id);
        $historia = $paciente ? $paciente->historiaClinica : null;

        foreach ($archivos as $archivo) {
            $nombreArchivo = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
            $ruta = $archivo->storeAs(
                'imagenes-clinicas/' . $request->paciente_id,
                $nombreArchivo,
                'public'
            );

            $imagen = ImagenClinica::create([
                'paciente_id'         => $request->paciente_id,
                'historia_clinica_id' => $request->historia_clinica_id ?? ($historia ? $historia->id : null),
                'evolucion_id'        => $request->evolucion_id,
                'user_id'             => auth()->id(),
                'tipo'                => $request->tipo,
                'titulo'              => $request->titulo,
                'descripcion'         => $request->descripcion,
                'archivo_path'        => $ruta,
                'archivo_nombre'      => $archivo->getClientOriginalName(),
                'archivo_tipo'        => $archivo->getMimeType(),
                'archivo_tamanio'     => $archivo->getSize(),
                'diente'              => $request->diente,
                'fecha_toma'          => $request->fecha_toma,
                'es_comparativo'      => $request->boolean('es_comparativo'),
                'grupo_comparativo'   => $request->grupo_comparativo,
                'orden_comparativo'   => $request->orden_comparativo,
            ]);

            $imagenesCreadas[] = $imagen;
        }

        return redirect()
            ->route('imagenes.galeria', $request->paciente_id)
            ->with('exito', count($imagenesCreadas) . ' imagen(es) subida(s) correctamente.');
    }

    public function show($id)
    {
        $imagen = ImagenClinica::with(['paciente', 'historiaClinica', 'evolucion', 'autor'])->findOrFail($id);

        $grupoImagenes = null;
        if ($imagen->es_comparativo && $imagen->grupo_comparativo) {
            $grupoImagenes = ImagenClinica::activas()
                ->where('grupo_comparativo', $imagen->grupo_comparativo)
                ->where('paciente_id', $imagen->paciente_id)
                ->orderBy('orden_comparativo')
                ->get();
        }

        return view('imagenes.show', compact('imagen', 'grupoImagenes'));
    }

    public function edit($id)
    {
        $imagen = ImagenClinica::with(['paciente'])->findOrFail($id);

        $tipos = [
            'fotografia_intraoral'   => 'Fotografía Intraoral',
            'fotografia_extraoral'   => 'Fotografía Extraoral',
            'radiografia_periapical' => 'Radiografía Periapical',
            'radiografia_panoramica' => 'Radiografía Panorámica',
            'radiografia_bitewing'   => 'Radiografía Bitewing',
            'foto_antes'             => 'Foto Antes del Tratamiento',
            'foto_durante'           => 'Foto Durante el Tratamiento',
            'foto_despues'           => 'Foto Después del Tratamiento',
            'foto_sonrisa'           => 'Foto de Sonrisa',
            'otra'                   => 'Otra',
        ];

        return view('imagenes.edit', compact('imagen', 'tipos'));
    }

    public function update(Request $request, $id)
    {
        $imagen = ImagenClinica::findOrFail($id);

        $request->validate([
            'tipo'              => 'required|string',
            'titulo'            => 'required|string|max:150',
            'descripcion'       => 'nullable|string',
            'fecha_toma'        => 'required|date',
            'diente'            => 'nullable|string|max:20',
            'es_comparativo'    => 'boolean',
            'orden_comparativo' => 'nullable|in:antes,durante,despues',
            'grupo_comparativo' => 'nullable|string|max:50',
        ]);

        $imagen->update([
            'tipo'              => $request->tipo,
            'titulo'            => $request->titulo,
            'descripcion'       => $request->descripcion,
            'fecha_toma'        => $request->fecha_toma,
            'diente'            => $request->diente,
            'es_comparativo'    => $request->boolean('es_comparativo'),
            'grupo_comparativo' => $request->grupo_comparativo,
            'orden_comparativo' => $request->orden_comparativo,
        ]);

        return redirect()
            ->route('imagenes.show', $imagen)
            ->with('exito', 'Imagen actualizada correctamente.');
    }

    public function destroy($id)
    {
        $imagen = ImagenClinica::findOrFail($id);
        $pacienteId = $imagen->paciente_id;

        Storage::disk('public')->delete($imagen->archivo_path);
        $imagen->update(['activo' => false]);

        return redirect()
            ->route('imagenes.galeria', $pacienteId)
            ->with('exito', 'Imagen eliminada correctamente.');
    }

    public function galeria($pacienteId)
    {
        $paciente = Paciente::findOrFail($pacienteId);

        $imagenes = ImagenClinica::activas()
            ->where('paciente_id', $pacienteId)
            ->orderBy('fecha_toma', 'desc')
            ->get();

        $porTipo = $imagenes->groupBy('tipo');

        $imagenesJs = $imagenes->map(fn($i) => [
            'id'    => $i->id,
            'url'   => asset('storage/' . $i->archivo_path),
            'title' => $i->titulo,
            'tipo'  => $i->tipo_label ?? $i->tipo,
            'desc'  => $i->descripcion ?? '',
        ])->values();

        return view('imagenes.galeria', compact('paciente', 'imagenes', 'porTipo', 'imagenesJs'));
    }

    public function comparativo($pacienteId)
    {
        $paciente = Paciente::findOrFail($pacienteId);

        $comparativas = ImagenClinica::activas()
            ->where('paciente_id', $pacienteId)
            ->where('es_comparativo', true)
            ->orderBy('fecha_toma')
            ->get();

        $grupos = $comparativas->groupBy('grupo_comparativo');

        $todasLasImagenes = ImagenClinica::activas()
            ->where('paciente_id', $pacienteId)
            ->orderBy('fecha_toma', 'desc')
            ->get();

        return view('imagenes.comparativo', compact('paciente', 'grupos', 'todasLasImagenes'));
    }

    public function asignarComparativo(Request $request)
    {
        $request->validate([
            'imagen_id'   => 'required|exists:imagenes_clinicas,id',
            'grupo'       => 'nullable|string',
            'orden'       => 'required|in:antes,despues',
            'paciente_id' => 'required|exists:pacientes,id',
        ]);

        $grupo = $request->grupo ?: null;

        // Quitar ese orden a cualquier imagen que ya lo tenga en el grupo
        $q = ImagenClinica::where('paciente_id', $request->paciente_id)
            ->where('orden_comparativo', $request->orden);
        if ($grupo) {
            $q->where('grupo_comparativo', $grupo);
        } else {
            $q->whereNull('grupo_comparativo');
        }
        $q->update(['orden_comparativo' => null]);

        // Asignar la nueva imagen al grupo y orden
        ImagenClinica::where('id', $request->imagen_id)->update([
            'es_comparativo'    => true,
            'grupo_comparativo' => $grupo,
            'orden_comparativo' => $request->orden,
        ]);

        return response()->json(['ok' => true]);
    }

    public function capturar(Request $request)
    {
        $request->validate([
            'paciente_id'   => 'required|exists:pacientes,id',
            'imagen_base64' => 'required|string',
            'tipo'          => 'required|string',
            'titulo'        => 'required|string',
            'fecha_toma'    => 'required|date',
        ]);

        $base64    = $request->imagen_base64;
        $base64    = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $imageData = base64_decode($base64);

        $nombreArchivo = time() . '_captura_' . uniqid() . '.jpg';
        $ruta = 'imagenes-clinicas/' . $request->paciente_id . '/' . $nombreArchivo;

        Storage::disk('public')->put($ruta, $imageData);

        $paciente = Paciente::find($request->paciente_id);
        $historia = $paciente ? $paciente->historiaClinica : null;

        $imagen = ImagenClinica::create([
            'paciente_id'         => $request->paciente_id,
            'historia_clinica_id' => $request->historia_clinica_id ?? ($historia ? $historia->id : null),
            'evolucion_id'        => $request->evolucion_id,
            'user_id'             => auth()->id(),
            'tipo'                => $request->tipo,
            'titulo'              => $request->titulo,
            'descripcion'         => $request->descripcion,
            'archivo_path'        => $ruta,
            'archivo_nombre'      => $nombreArchivo,
            'archivo_tipo'        => 'image/jpeg',
            'archivo_tamanio'     => strlen($imageData),
            'diente'              => $request->diente,
            'fecha_toma'          => $request->fecha_toma,
            'es_comparativo'      => $request->boolean('es_comparativo'),
            'orden_comparativo'   => $request->orden_comparativo,
        ]);

        return response()->json([
            'success' => true,
            'imagen'  => [
                'id'     => $imagen->id,
                'url'    => $imagen->url,
                'titulo' => $imagen->titulo,
                'numero' => $imagen->numero_imagen,
            ],
        ]);
    }
}
