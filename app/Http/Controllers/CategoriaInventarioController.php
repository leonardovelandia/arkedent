<?php

namespace App\Http\Controllers;

use App\Models\CategoriaInventario;
use Illuminate\Http\Request;

class CategoriaInventarioController extends Controller
{
    public function index()
    {
        $categorias = CategoriaInventario::withCount('materiales')->orderBy('nombre')->get();
        return view('inventario.categorias', compact('categorias'));
    }

    public function create()
    {
        return view('inventario.categorias');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:categorias_inventario,nombre',
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
        ]);

        CategoriaInventario::create($validated);

        return redirect()->route('inventario-categorias.index')
            ->with('exito', 'Categoría creada correctamente.');
    }

    public function edit($id)
    {
        $categoria  = CategoriaInventario::findOrFail($id);
        $categorias = CategoriaInventario::withCount('materiales')->orderBy('nombre')->get();
        return view('inventario.categorias', compact('categoria', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaInventario::findOrFail($id);

        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:categorias_inventario,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
        ]);

        $categoria->update($validated);

        return redirect()->route('inventario-categorias.index')
            ->with('exito', 'Categoría actualizada correctamente.');
    }

    public function destroy($id)
    {
        $categoria = CategoriaInventario::withCount('materiales')->findOrFail($id);

        if ($categoria->materiales_count > 0) {
            return redirect()->route('inventario-categorias.index')
                ->with('error', 'No se puede eliminar una categoría con materiales asociados.');
        }

        $categoria->delete();

        return redirect()->route('inventario-categorias.index')
            ->with('exito', 'Categoría eliminada correctamente.');
    }
}
