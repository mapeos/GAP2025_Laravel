<?php

namespace App\Http\Controllers;

use App\Models\TipoEvento;
use Illuminate\Http\Request;

class TipoEventoController extends Controller
{
    public function index()
    {
        return response()->json(TipoEvento::all());
    }

    public function store(Request $request)
    {
        $tipoEvento = TipoEvento::create($request->all());
        return response()->json($tipoEvento, 201);
    }

    public function show(string $id)
    {
        $tipoEvento = TipoEvento::findOrFail($id);
        return response()->json($tipoEvento);
    }

    public function update(Request $request, $id)
    {
        $tipoEvento = TipoEvento::findOrFail($id);
        $tipoEvento->update($request->all());
        return response()->json($tipoEvento);
    }

    public function destroy($id)
    {
        $tipoEvento = TipoEvento::findOrFail($id);
        $tipoEvento->delete();
        return response()->json(null, 204);
    }
}
