<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Material;
use App\Models\ItemCompra;
use App\Traits\FormateaCampos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    use FormateaCampos;

    public function index(Request $request)
    {
        $query = Compra::where('activo', true)->with('proveedor');

        if ($request->filled('buscar')) {
            $q = '%' . $request->buscar . '%';
            $query->where(function ($sq) use ($q) {
                $sq->where('numero_compra', 'like', $q)
                   ->orWhere('numero_factura', 'like', $q)
                   ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', $q));
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha_compra', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha_compra', '<=', $request->hasta);
        }

        $compras = $query->withCount('items')->orderByDesc('fecha_compra')->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('proveedores.compras._tabla', compact('compras'));
        }

        // Resumen
        $inicioMes  = now()->startOfMonth();
        $inicioAnio = now()->startOfYear();

        $totalMes  = Compra::where('activo', true)->where('estado', 'pagada')->whereDate('fecha_compra', '>=', $inicioMes)->sum('total');
        $totalAnio = Compra::where('activo', true)->where('estado', 'pagada')->whereDate('fecha_compra', '>=', $inicioAnio)->sum('total');
        $pendiente = Compra::where('activo', true)->where('estado', 'pendiente')->sum('total');
        $numProveedores = Proveedor::activos()->count();

        $proveedoresList = Proveedor::activos()->orderBy('nombre')->get(['id', 'nombre']);

        return view('proveedores.compras.index', compact(
            'compras', 'totalMes', 'totalAnio', 'pendiente', 'numProveedores', 'proveedoresList'
        ));
    }

    public function create(Request $request)
    {
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $materiales  = Material::activos()->orderBy('nombre')->get(['id', 'nombre', 'unidad_medida', 'precio_unitario']);
        $proveedor   = $request->filled('proveedor_id') ? Proveedor::find($request->proveedor_id) : null;

        return view('proveedores.compras.create', compact('proveedores', 'materiales', 'proveedor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id'              => 'required|exists:proveedores,id',
            'fecha_compra'              => 'required|date',
            'numero_factura'            => 'nullable|string|max:50',
            'metodo_pago'               => 'required|string',
            'fecha_vencimiento'         => 'nullable|date|after_or_equal:fecha_compra',
            'items'                     => 'required|array|min:1',
            'items.*.descripcion'       => 'required|string|max:255',
            'items.*.cantidad'          => 'required|numeric|min:0.01',
            'items.*.unidad_medida'     => 'required|string',
            'items.*.precio_unitario'   => 'required|numeric|min:0',
            'items.*.material_id'       => 'nullable|exists:materiales,id',
        ]);

        DB::beginTransaction();
        try {
            // Calcular totales
            $subtotal  = 0;
            $itemsData = [];
            foreach ($request->items as $item) {
                $precio   = (float) str_replace(['.', ','], ['', '.'], $item['precio_unitario']);
                $cantidad = (float) $item['cantidad'];
                $total    = $cantidad * $precio;
                $subtotal += $total;
                $itemsData[] = [
                    'material_id'    => $item['material_id'] ?? null,
                    'descripcion'    => $item['descripcion'],
                    'cantidad'       => $cantidad,
                    'unidad_medida'  => $item['unidad_medida'],
                    'precio_unitario'=> $precio,
                    'valor_total'    => $total,
                ];
            }

            $descuento = (float) str_replace(['.', ','], ['', '.'], $request->descuento_valor ?? 0);
            $total     = $subtotal - $descuento;

            $compra = Compra::create([
                'proveedor_id'    => $request->proveedor_id,
                'user_id'         => Auth::id(),
                'fecha_compra'    => $request->fecha_compra,
                'numero_factura'  => $request->numero_factura,
                'subtotal'        => $subtotal,
                'descuento_valor' => $descuento,
                'total'           => $total,
                'metodo_pago'     => $request->metodo_pago,
                'estado'          => 'pendiente',
                'fecha_vencimiento'=> $request->fecha_vencimiento,
                'notas'           => $request->notas,
            ]);

            $proveedor = Proveedor::find($request->proveedor_id);

            foreach ($itemsData as $itemDato) {
                $item = $compra->items()->create($itemDato);

                if (!empty($itemDato['material_id'])) {
                    $material = Material::find($itemDato['material_id']);
                    if ($material) {
                        $material->registrarEntrada(
                            $itemDato['cantidad'],
                            "Compra {$compra->numero_formateado} — Proveedor: {$proveedor->nombre}",
                            Auth::id(),
                            $itemDato['precio_unitario'],
                            $proveedor->nombre,
                            $compra->numero_factura
                        );
                        ItemCompra::where('id', $item->id)
                            ->update(['actualizo_inventario' => true]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('compras.show', $compra)
                ->with('success', "Compra {$compra->numero_formateado} registrada correctamente. El inventario fue actualizado.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'registradoPor', 'items.material'])->findOrFail($id);
        return view('proveedores.compras.show', compact('compra'));
    }

    public function edit($id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado !== 'pendiente') {
            return redirect()->route('compras.show', $compra)
                ->with('error', 'Solo se pueden editar compras en estado pendiente.');
        }

        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $materiales  = Material::activos()->orderBy('nombre')->get(['id', 'nombre', 'unidad_medida', 'precio_unitario']);

        return view('proveedores.compras.edit', compact('compra', 'proveedores', 'materiales'));
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado !== 'pendiente') {
            return redirect()->route('compras.show', $compra)
                ->with('error', 'Solo se pueden editar compras en estado pendiente.');
        }

        $request->validate([
            'numero_factura'    => 'nullable|string|max:50',
            'fecha_vencimiento' => 'nullable|date',
            'notas'             => 'nullable|string',
        ]);

        $compra->update([
            'numero_factura'   => $request->numero_factura,
            'fecha_vencimiento'=> $request->fecha_vencimiento,
            'notas'            => $request->notas,
        ]);

        return redirect()->route('compras.show', $compra)
            ->with('success', 'Compra actualizada.');
    }

    public function pagar(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden marcar como pagadas las compras pendientes.');
        }

        $compra->update(['estado' => 'pagada']);

        return back()->with('success', "Compra {$compra->numero_formateado} marcada como pagada.");
    }

    public function cancelar(Request $request, $id)
    {
        $compra = Compra::with('items.material')->findOrFail($id);

        if ($compra->estado === 'cancelada') {
            return back()->with('error', 'La compra ya está cancelada.');
        }

        DB::beginTransaction();
        try {
            // Revertir inventario para items que lo actualizaron
            foreach ($compra->items as $item) {
                if ($item->actualizo_inventario && $item->material) {
                    $item->material->registrarSalida(
                        (float) $item->cantidad,
                        "Reversión por cancelación de compra {$compra->numero_formateado}",
                        Auth::id()
                    );
                    $item->update(['actualizo_inventario' => false]);
                }
            }

            $compra->update(['estado' => 'cancelada']);

            DB::commit();

            return back()->with('success', "Compra {$compra->numero_formateado} cancelada. El inventario fue revertido.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->update(['activo' => false]);
        return redirect()->route('compras.index')->with('success', 'Compra eliminada.');
    }
}
