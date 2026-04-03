<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\CategoriaInventario;
use App\Models\MovimientoInventario;
use App\Traits\FormateaCampos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    use FormateaCampos;

    public function index(Request $request)
    {
        $categoriaId = $request->input('categoria_id');
        $estado      = $request->input('estado');
        $buscar      = $request->input('buscar');

        $mostrarInactivos = $estado === 'inactivo';
        $query = Material::with('categoria')->where('activo', !$mostrarInactivos);

        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        }

        if (!$mostrarInactivos) {
            if ($estado === 'critico') {
                $query->whereColumn('stock_actual', '<=', 'stock_minimo');
            } elseif ($estado === 'bajo') {
                $query->whereRaw('stock_actual > stock_minimo AND stock_actual <= stock_minimo * 1.5');
            } elseif ($estado === 'normal') {
                $query->whereRaw('stock_actual > stock_minimo * 1.5');
            }
        }

        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50])
            ? (int) $request->input('per_page', 10) : 10;
        $materiales = $query->orderBy('nombre')->paginate($perPage)->withQueryString();

        $categorias  = CategoriaInventario::activas()->orderBy('nombre')->get();
        $alertas     = Material::stockBajo()->where('activo', true)->get();

        $totalActivos  = Material::where('activo', true)->count();
        $totalNormal   = Material::where('activo', true)->whereRaw('stock_actual > stock_minimo * 1.5')->count();
        $totalBajo     = Material::where('activo', true)->whereRaw('stock_actual > stock_minimo AND stock_actual <= stock_minimo * 1.5')->count();
        $totalCritico  = Material::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        $valorTotal    = Material::where('activo', true)->whereNotNull('precio_unitario')
                            ->selectRaw('SUM(stock_actual * precio_unitario) as total')
                            ->value('total') ?? 0;

        return view('inventario.index', compact(
            'materiales', 'categorias', 'alertas',
            'totalActivos', 'totalNormal', 'totalBajo', 'totalCritico', 'valorTotal',
            'categoriaId', 'estado', 'buscar'
        ));
    }

    public function create()
    {
        $categorias = CategoriaInventario::activas()->orderBy('nombre')->get();
        return view('inventario.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'             => 'required|string|max:150',
            'codigo'             => 'nullable|string|max:20|unique:materiales,codigo',
            'descripcion'        => 'nullable|string',
            'categoria_id'       => 'nullable|exists:categorias_inventario,id',
            'unidad_medida'      => 'required|string|max:30',
            'stock_actual'       => 'required|numeric|min:0',
            'stock_minimo'       => 'required|numeric|min:0',
            'stock_maximo'       => 'nullable|numeric|min:0',
            'precio_unitario'    => 'nullable|numeric|min:0',
            'proveedor_habitual' => 'nullable|string|max:150',
            'ubicacion'          => 'nullable|string|max:100',
        ]);

        $material = Material::create($validated);

        // Registrar el stock inicial como movimiento de entrada
        if ((float) $validated['stock_actual'] > 0) {
            MovimientoInventario::create([
                'material_id'      => $material->id,
                'user_id'          => Auth::id(),
                'tipo'             => 'entrada',
                'cantidad'         => $validated['stock_actual'],
                'stock_anterior'   => 0,
                'stock_posterior'  => $validated['stock_actual'],
                'concepto'         => 'Stock inicial al crear el material',
                'fecha_movimiento' => now()->toDateString(),
            ]);
        }

        return redirect()->route('inventario.show', $material)
            ->with('exito', 'Material creado correctamente.');
    }

    public function show($id)
    {
        $material = Material::with('categoria')->findOrFail($id);
        $movimientos = MovimientoInventario::with(['usuario', 'evolucion.paciente'])
            ->where('material_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('inventario.show', compact('material', 'movimientos'));
    }

    public function edit($id)
    {
        $material   = Material::findOrFail($id);
        $categorias = CategoriaInventario::activas()->orderBy('nombre')->get();
        return view('inventario.edit', compact('material', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'nombre'             => 'required|string|max:150',
            'codigo'             => 'nullable|string|max:20|unique:materiales,codigo,' . $id,
            'descripcion'        => 'nullable|string',
            'categoria_id'       => 'nullable|exists:categorias_inventario,id',
            'unidad_medida'      => 'required|string|max:30',
            'stock_minimo'       => 'required|numeric|min:0',
            'stock_maximo'       => 'nullable|numeric|min:0',
            'precio_unitario'    => 'nullable|numeric|min:0',
            'proveedor_habitual' => 'nullable|string|max:150',
            'ubicacion'          => 'nullable|string|max:100',
        ]);

        $material->update($validated);

        return redirect()->route('inventario.show', $material)
            ->with('exito', 'Material actualizado correctamente.');
    }

    public function entrada(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'cantidad'        => 'required|numeric|min:0.01',
            'precio_unitario' => 'nullable|numeric|min:0',
            'proveedor'       => 'nullable|string|max:150',
            'numero_factura'  => 'nullable|string|max:50',
            'observaciones'   => 'nullable|string',
            'fecha_movimiento'=> 'required|date',
        ]);

        $material->registrarEntrada(
            (float) $validated['cantidad'],
            'Entrada de mercancía' . ($validated['numero_factura'] ? ' — Factura: ' . $validated['numero_factura'] : ''),
            Auth::id(),
            isset($validated['precio_unitario']) ? (float) $validated['precio_unitario'] : null,
            $validated['proveedor'] ?? null,
            $validated['numero_factura'] ?? null
        );

        // Actualizar observaciones en el último movimiento si las hay
        if (!empty($validated['observaciones'])) {
            $material->movimientos()->first()?->update(['observaciones' => $validated['observaciones']]);
        }

        // Actualizar fecha del movimiento
        $material->movimientos()->first()?->update(['fecha_movimiento' => $validated['fecha_movimiento']]);

        // Actualizar precio unitario del material si se proporcionó
        if (!empty($validated['precio_unitario'])) {
            $material->update(['precio_unitario' => $validated['precio_unitario']]);
        }

        return redirect()->route('inventario.show', $material)
            ->with('exito', 'Entrada de ' . number_format($validated['cantidad'], 2) . ' ' . $material->unidad_medida . ' registrada.');
    }

    public function ajuste(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'stock_nuevo' => 'required|numeric|min:0',
            'motivo'      => 'required|string|max:255',
        ]);

        $material->ajustarStock(
            (float) $validated['stock_nuevo'],
            $validated['motivo'],
            Auth::id()
        );

        return redirect()->route('inventario.show', $material)
            ->with('exito', 'Stock ajustado a ' . number_format($validated['stock_nuevo'], 2) . ' ' . $material->unidad_medida . '.');
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->update(['activo' => false]);

        return redirect()->route('inventario.index')
            ->with('exito', 'Material desactivado correctamente.');
    }

    public function activar($id)
    {
        $material = Material::findOrFail($id);
        $material->update(['activo' => true]);

        return redirect()->route('inventario.index', ['estado' => 'inactivo'])
            ->with('exito', 'Material "' . $material->nombre . '" activado correctamente.');
    }
}
