<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Compra;
use App\Models\Material;
use App\Traits\FormateaCampos;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    use FormateaCampos;

    public function index(Request $request)
    {
        $query = Proveedor::activos();

        if ($request->filled('buscar')) {
            $q = '%' . $request->buscar . '%';
            $query->where(function ($sq) use ($q) {
                $sq->where('nombre', 'like', $q)
                   ->orWhere('nit', 'like', $q)
                   ->orWhere('contacto', 'like', $q);
            });
        }

        if ($request->filled('categoria')) {
            $query->whereJsonContains('categorias', $request->categoria);
        }

        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50])
            ? (int) $request->input('per_page', 10) : 10;
        $proveedores = $query->withCount(['compras as total_ordenes' => function ($q) {
            $q->where('activo', true);
        }])->withSum(['compras as total_compras' => function ($q) {
            $q->where('activo', true)->where('estado', 'pagada');
        }], 'total')
          ->orderBy('nombre')->paginate($perPage)->withQueryString();

        // Resumen
        $totalActivos = Proveedor::activos()->count();

        $inicioMes = now()->startOfMonth();
        $comprasMes = Compra::where('activo', true)
            ->where('estado', 'pagada')
            ->whereDate('fecha_compra', '>=', $inicioMes)
            ->sum('total');

        $proveedorFrecuente = Compra::where('activo', true)
            ->whereDate('fecha_compra', '>=', $inicioMes)
            ->selectRaw('proveedor_id, COUNT(*) as total')
            ->groupBy('proveedor_id')
            ->orderByDesc('total')
            ->with('proveedor:id,nombre')
            ->first();

        $comprasPendientes = Compra::where('activo', true)
            ->where('estado', 'pendiente')
            ->sum('total');

        $categorias = Proveedor::etiquetasCategorias();

        return view('proveedores.index', compact(
            'proveedores', 'totalActivos', 'comprasMes',
            'proveedorFrecuente', 'comprasPendientes', 'categorias'
        ));
    }

    public function create()
    {
        $categorias = Proveedor::etiquetasCategorias();
        return view('proveedores.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'           => 'required|string|max:150',
            'codigo'           => 'nullable|string|max:20|unique:proveedores,codigo',
            'nit'              => 'nullable|string|max:30',
            'contacto'         => 'nullable|string|max:150',
            'telefono'         => 'nullable|string|max:30',
            'whatsapp'         => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:120',
            'direccion'        => 'nullable|string|max:255',
            'ciudad'           => 'nullable|string|max:100',
            'tiempo_entrega_dias' => 'nullable|integer|min:0',
            'condiciones_pago' => 'nullable|string|max:255',
            'calificacion'     => 'nullable|numeric|min:1|max:5',
            'categorias'       => 'nullable|array',
            'notas'            => 'nullable|string',
        ]);

        $datos = $this->formatearDatos($validated);
        $datos['categorias'] = $request->categorias ?? [];

        Proveedor::create($datos);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function show($id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $compras = $proveedor->compras()
            ->where('activo', true)
            ->paginate(10);

        $totalHistorico = $proveedor->compras()->where('activo', true)->where('estado', 'pagada')->sum('total');
        $totalAnio      = $proveedor->compras()->where('activo', true)->where('estado', 'pagada')->whereYear('fecha_compra', now()->year)->sum('total');
        $totalMes       = $proveedor->compras()->where('activo', true)->where('estado', 'pagada')->whereMonth('fecha_compra', now()->month)->whereYear('fecha_compra', now()->year)->sum('total');
        $numOrdenes     = $proveedor->compras()->where('activo', true)->count();

        // Materiales más comprados a este proveedor
        $materiales = Material::whereHas('itemsCompra', function ($q) use ($proveedor) {
            $q->whereHas('compra', fn($sq) => $sq->where('proveedor_id', $proveedor->id)->where('estado', 'pagada')->where('activo', true));
        })->with(['itemsCompra' => function ($q) use ($proveedor) {
            $q->whereHas('compra', fn($sq) => $sq->where('proveedor_id', $proveedor->id)->where('estado', 'pagada'));
        }])->get();

        $promedioPorCompra = $proveedor->compras()->where('activo', true)->where('estado', 'pagada')->avg('total');

        return view('proveedores.show', compact(
            'proveedor', 'compras', 'totalHistorico', 'totalAnio',
            'totalMes', 'numOrdenes', 'materiales', 'promedioPorCompra'
        ));
    }

    public function edit($id)
    {
        $proveedor  = Proveedor::findOrFail($id);
        $categorias = Proveedor::etiquetasCategorias();
        return view('proveedores.edit', compact('proveedor', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'nombre'           => 'required|string|max:150',
            'codigo'           => 'nullable|string|max:20|unique:proveedores,codigo,' . $id,
            'nit'              => 'nullable|string|max:30',
            'contacto'         => 'nullable|string|max:150',
            'telefono'         => 'nullable|string|max:30',
            'whatsapp'         => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:120',
            'direccion'        => 'nullable|string|max:255',
            'ciudad'           => 'nullable|string|max:100',
            'tiempo_entrega_dias' => 'nullable|integer|min:0',
            'condiciones_pago' => 'nullable|string|max:255',
            'calificacion'     => 'nullable|numeric|min:1|max:5',
            'categorias'       => 'nullable|array',
            'notas'            => 'nullable|string',
            'activo'           => 'boolean',
        ]);

        $datos = $this->formatearDatos($validated);
        $datos['categorias'] = $request->categorias ?? [];
        $datos['activo']     = $request->boolean('activo', true);

        $proveedor->update($datos);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update(['activo' => false]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor desactivado.');
    }

    public function comparar(Request $request)
    {
        $materiales = Material::activos()->orderBy('nombre')->get(['id', 'nombre', 'unidad_medida']);
        $material   = null;
        $comparacion = collect();
        $historial   = collect();

        if ($request->filled('material_id')) {
            $material = Material::find($request->material_id);
            if ($material) {
                // Historial de precios
                $historial = $material->itemsCompra()
                    ->with('compra.proveedor')
                    ->whereHas('compra', fn($q) => $q->where('estado', 'pagada')->where('activo', true))
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get();

                // Comparación por proveedor
                $comparacion = $historial->groupBy('compra.proveedor_id')
                    ->map(function ($items, $provId) {
                        $proveedor = $items->first()->compra->proveedor;
                        return [
                            'proveedor'       => $proveedor,
                            'ultimo_precio'   => $items->sortByDesc('created_at')->first()->precio_unitario,
                            'precio_promedio' => $items->avg('precio_unitario'),
                            'ultima_compra'   => $items->sortByDesc('created_at')->first()->compra->fecha_compra,
                            'num_compras'     => $items->count(),
                        ];
                    })
                    ->values()
                    ->sortBy('ultimo_precio');
            }
        }

        return view('proveedores.comparar', compact('materiales', 'material', 'comparacion', 'historial'));
    }
}
