<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoParticipante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EventoParticipanteController extends Controller
{
    /**
     * Actualiza el estado de asistencia de un participante.
     */

     public function updateAsistencia(Request $request, Evento $evento)
     {
        $validator = Validator::make($request->all(), [
            'estado_asistencia' => 'required|in:confirmado,pendiente,rechazado',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
               
        }

        $participante = $evento->participantes()
            ->where('user_id', Auth::id())
            ->first();

        if (!$participante) {
            return response()->json([
                'success' => false,
                'message' => 'No eres participante de este evento.'
            ], 403);
        }

        $participante->pivot->update([
            'estado_asistencia' => $request->estado_asistencia,
            'notas' => $request->notas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de asistencia actualizado correctamente.'
        ]);
     }

     /**
      * Añade participantes a un evento
      */
     public function addParticipantes(Request $request, Evento $evento)
     {
        $validator = Validator::make($request->all(), [
            'participantes' => 'required|array',
            'participantes.*' => 'exists:users,id',
            'rol' => 'required|in:profesor,alumno,invitado',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $evento->participantes()->attach($request->participantes, [
            'rol' => $request->rol,
            'estado_asistencia' => 'pendiente',
            'status' => true,
        ]);

        return redirect()->route('admin.events.show', $evento)
            ->with('success', 'Participantes añadidos exitosamente.');
     }

     /**
      * Elimina participantes de un evento
      */
     public function removeParticipantes(Request $request, Evento $evento)
     {
        $validator = Validator::make($request->all(), [
            'participantes' => 'required|array',
            'participantes.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $evento->participantes()->detach($request->participantes);

        return redirect()->route('admin.events.show', $evento)
            ->with('success', 'Participantes eliminados exitosamente.');
     }

     /**
      * Actualiza el rol de un participante
      */
     public function updateRol(Request $request, Evento $evento)
     {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'rol' => 'required|in:profesor,alumno,invitado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $participante = $evento->participantes()
            ->where('user_id', $request->user_id)
            ->first();

        if (!$participante) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no es participante de este evento.'
            ], 404);
        }

        $participante->pivot->update([
            'rol' => $request->rol,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado correctamente.'
        ]);
     }

}
