<?php

namespace App\Http\Controllers;

use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoEventoController extends Controller
{
    /**
     * Muestra una lista de los tipos de eventos
     */
    public function index()
    {
        $tiposEvento = TipoEvento::all();
        return view('admin.events.types.index', compact('tiposEvento'));
    }

    /**
     * Muestra el formulario para crear un nuevo tipo de evento
     */
    public function create()
    {
        return view('admin.events.types.create');
    }

    /**
     * Almacena un nuevo tipo de evento
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:tipos_evento',
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TipoEvento::create($request->all());
        return redirect()->route('admin.events.types.index')
            ->with('success', 'Tipo de evento creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un tipo de evento
     */

    public function edit(TipoEvento $tipoEvento)
    {
        return view('admin.events.types.edit', compact('tipoEvento'));
    }

    /**
     * Actualiza un tipo de evento específico
     */
    public function update(Request $request, TipoEvento $tipoEvento)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:tipos_evento,nombre,' . $tipoEvento->id,
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $tipoEvento->update($request->all());
        return redirect()->route('admin.events.types.index')
            ->with('success', 'Tipo de evento actualizado exitosamente.');
    }
    
    /**
     * Elimina un tipo de evento específico
     */

    public function destroy(TipoEvento $tipoEvento)
    {
        if ($tipoEvento->eventos()->exists()) {
            return redirect()->route('admin.events.types.index')
                ->with('error', 'No se puede eliminar un tipo de evento que tiene eventos asociados.');
        }

        $tipoEvento->delete();
        return redirect()->route('admin.events.types.index')
            ->with('success', 'Tipo de evento eliminado exitosamente.');
    }
}
