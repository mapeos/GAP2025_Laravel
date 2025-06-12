<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EventoController extends Controller
{
    /**
     * Muestra una lista de eventos
     */
    public function index()
    {
        $eventos = Evento::with(['tipoEvento', 'participantes'])->get();
        return view('admin.events.index', compact('eventos'));
    }

    /**
     * Muestra el calendario de eventos
     */
    public function calendario()
    {
        $eventos = Evento::with(['tipoEvento', 'participantes'])
            ->get()
            ->map(function ($evento) {
                return [
                    'id' => $evento->id,
                    'title' => $evento->titulo,
                    'start' => $evento->fecha_inicio,
                    'end' => $evento->fecha_fin,
                    'color' => $evento->tipoEvento->color,
                    'descripcion' => $evento->descripcion,
                    'url' => route('events.show', $evento->id)
                ];
            });

        $profesores = \App\Models\User::role('profesor')->get();
        $alumnos = \App\Models\User::role('alumno')->get();

        return view('events.calendar', compact('eventos', 'profesores', 'alumnos'));
    }

    /**
     * Obtiene los eventos para el calendario en formato JSON
     */
    public function getEventos()
    {
        $eventos = Evento::with(['tipoEvento', 'participantes'])
            ->get()
            ->map(function ($evento) {
                return [
                    'id' => $evento->id,
                    'title' => $evento->titulo,
                    'start' => $evento->fecha_inicio,
                    'end' => $evento->fecha_fin,
                    'color' => $evento->tipoEvento->color,
                    'descripcion' => $evento->descripcion,
                    'url' => route('events.show', $evento->id)
                ];
            });

        return response()->json($eventos);
    }

    /**
     * Muestra el formulario para crear un nuevo evento
     */
    public function create()
    {
        $tiposEvento = TipoEvento::where('status', true)->get();
        return view('admin.events.create', compact('tiposEvento'));
    }

    /**
     * Almacena un nuevo evento
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
            'url_virtual' => 'nullable|url|max:255',
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['creado_por'] = Auth::id();

        $evento = Evento::create($data);

        // Si la petición es AJAX, responde con JSON
        if ($request->expectsJson()) {
            // Cargar la relación tipoEvento para obtener el color
            $evento->load('tipoEvento');

            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'evento' => [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'descripcion' => $evento->descripcion,
                    'fecha_inicio' => $evento->fecha_inicio->toISOString(),
                    'fecha_fin' => $evento->fecha_fin->toISOString(),
                    'color' => $evento->tipoEvento->color
                ]
            ]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento creado exitosamente.');
    }

    /**
     * Muestra un evento específico
     */
    public function show(Evento $evento)
    {
        return view('admin.events.show', compact('evento'));
    }

    /**
     * Muestra el formulario para editar un evento
     */
    public function edit(Evento $evento)
    {
        $tiposEvento = TipoEvento::where('status', true)->get();
        return view('admin.events.edit', compact('evento', 'tiposEvento'));
    }

    /**
     * Actualiza un evento específico (soporta AJAX y formulario)
     */
    public function update(Request $request, Evento $evento)
    {
        // Si la petición es AJAX, responde con JSON
        if ($request->expectsJson()) {
            $validator = Validator::make($request->all(), [
                'titulo' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                $evento->update($request->only([
                    'titulo',
                    'descripcion',
                    'fecha_inicio',
                    'fecha_fin'
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado exitosamente'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el evento: ' . $e->getMessage()
                ], 500);
            }
        }

        // Petición normal (formulario)
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
            'url_virtual' => 'nullable|url|max:255',
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $evento->update($request->all());
        return redirect()->route('admin.events.index')
            ->with('success', 'Evento actualizado exitosamente.');
    }

    /**
     * Elimina un evento específico (soporta AJAX y formulario)
     */
    public function destroy(Evento $evento)
    {
        // Si la petición es AJAX, responde con JSON
        if (request()->expectsJson()) {
            $evento->delete();
            return response()->json(['success' => true]);
        }

        // Petición normal (formulario)
        $evento->delete();
        return redirect()->route('admin.events.index')
            ->with('success', 'Evento eliminado exitosamente.');
    }

    /**
     * Muestra el formulario para crear un recordatorio personal
     */
    public function createReminder()
    {
        return view('events.reminders.create');
    }

    /**
     * Guarda un nuevo recordatorio personal
     */
    public function storeReminder(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $evento = Evento::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'tipo_evento_id' => 1, // ID del tipo "Recordatorio Personal"
            'creado_por' => Auth::id(),
            'status' => true,
        ]);

        // Añadir al usuario como participante
        $evento->participantes()->attach(Auth::id(), [
            'rol' => 'Creador',
            'estado_asistencia' => 'confirmado',
            'status' => true,
        ]);

        return redirect()->route('events.calendar')
            ->with('success', 'Recordatorio creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un recordatorio personal
     */
    public function editReminder(Evento $evento)
    {
        // Verificar que el usuario es el creador del recordatorio
        if ($evento->creado_por !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este recordatorio.');
        }

        return view('events.reminders.edit', compact('evento'));
    }

    /**
     * Actualiza un recordatorio personal
     */
    public function updateReminder(Request $request, Evento $evento)
    {
        // Verificar que el usuario es el creador del recordatorio
        if ($evento->creado_por !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este recordatorio.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $evento->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        return redirect()->route('events.calendar')
            ->with('success', 'Recordatorio actualizado exitosamente.');
    }

    /**
     * Elimina un recordatorio personal
     */
    public function destroyReminder(Evento $evento)
    {
        // Verificar que el usuario es el creador del recordatorio
        if ($evento->creado_por !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar este recordatorio.');
        }

        $evento->delete();

        return redirect()->route('events.calendar')
            ->with('success', 'Recordatorio eliminado exitosamente.');
    }
}
