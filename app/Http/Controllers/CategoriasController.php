<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = Categorias::withTrashed()->paginate(10);
        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:categorias,nombre|max:45',
            'descripcion' => 'nullable|max:255',
        ]);

        Categorias::create($request->all());

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada con éxito');
    }

    public function edit($id)
    {
        $categoria = Categorias::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = Categorias::findOrFail($id);

        $request->validate([
            'nombre' => 'required|max:45|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|max:255',
        ]);

        $categoria->update($request->all());

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada');
    }

    public function destroy($id)
    {
        Categorias::findOrFail($id)->delete();
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada');
    }

    public function restore($id)
    {
        $categoria = Categorias::onlyTrashed()->findOrFail($id);
        $categoria->restore();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría restaurada correctamente.');
    }
}
