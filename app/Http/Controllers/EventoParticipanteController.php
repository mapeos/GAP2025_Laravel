<?php

namespace App\Http\Controllers;

use App\Models\EventoParticipante;
use Illuminate\Http\Request;

class EventoParticipanteController extends Controller
{

    public function index()
    {
        return response()->json(EventoParticipante::all());
    }


    public function store(Request $request)
    {
        $eventoParticipante = EventoParticipante::create($request->all());
        return response()->json($eventoParticipante, 201);
    }


    public function show(string $id)
    {
        $eventoParticipante = EventoParticipante::findOrFail($id);
        return response()->json($eventoParticipante);
    }


    public function update(Request $request, string $id)
    {
        $eventoParticipante = EventoParticipante::findOrFail($id);
        $eventoParticipante->update($request->all());
        return response()->json($eventoParticipante);
    }


    public function destroy(string $id)
    {
        $eventoParticipante = EventoParticipante::findOrFail($id);
        $eventoParticipante->delete();
        return response()->json(null, 204);
    }
}
