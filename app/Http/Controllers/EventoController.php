<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        $userId = Auth::id();
        $tipoRecordatorio = TipoEvento::where('nombre', 'Recordatorio Personal')->first();

        $query = Evento::with(['tipoEvento', 'participantes', 'creador']);

        // Si es un recordatorio personal, solo mostrar los creados por el usuario actual
        if ($tipoRecordatorio) {
            $query->where(function($q) use ($userId, $tipoRecordatorio) {
                $q->where('tipo_evento_id', '!=', $tipoRecordatorio->id)
                  ->orWhere(function($q2) use ($userId, $tipoRecordatorio) {
                      $q2->where('tipo_evento_id', $tipoRecordatorio->id)
                        ->where('creado_por', $userId);
                  });
            });
        }

        $eventos = $query->get()
            ->map(function ($evento) {
                return [
                    'id' => $evento->id,
                    'title' => $evento->titulo,
                    'start' => $evento->fecha_inicio,
                    'end' => $evento->fecha_fin,
                    'color' => $evento->tipoEvento->color ?? '#3788d8',
                    'extendedProps' => [
                        'descripcion' => $evento->descripcion,
                        'ubicacion' => $evento->ubicacion,
                        'url_virtual' => $evento->url_virtual,
                        'tipo_evento_id' => $evento->tipo_evento_id,
                        'tipo_evento_nombre' => $evento->tipoEvento->nombre ?? 'N/A',
                        'status' => $evento->status,
                        'creado_por' => $evento->creado_por,
                        'creado_por_nombre' => $evento->creador->name ?? 'N/A',
                        'created_at' => $evento->created_at,
                        'participantes' => $evento->participantes->map(function($participante) {
                            return [
                                'id' => $participante->id,
                                'name' => $participante->name,
                                'rol' => $participante->pivot->rol,
                                'estado_asistencia' => $participante->pivot->estado_asistencia
                            ];
                        })
                    ]
                ];
            });

        $profesores = User::role('profesor')->get();
        $alumnos = User::role('alumno')->get();

        return view('events.calendar', compact('eventos', 'profesores', 'alumnos'));
    }

    /**
     * Obtiene los eventos para el calendario en formato JSON
     */
    public function getEventos()
    {
        $userId = Auth::id();
        $tipoRecordatorio = TipoEvento::where('nombre', 'Recordatorio Personal')->first();

        $query = Evento::with(['tipoEvento', 'participantes']);

        // Si es un recordatorio personal, solo mostrar los creados por el usuario actual
        if ($tipoRecordatorio) {
            $query->where(function($q) use ($userId, $tipoRecordatorio) {
                $q->where('tipo_evento_id', '!=', $tipoRecordatorio->id)
                  ->orWhere(function($q2) use ($userId, $tipoRecordatorio) {
                      $q2->where('tipo_evento_id', $tipoRecordatorio->id)
                        ->where('creado_por', $userId);
                  });
            });
        }

        $eventos = $query->get()
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

        // Verificar si el usuario es alumno y forzar tipo de evento a Recordatorio Personal
        if (Auth::user()->hasRole('alumno')) {
            $recordatorioPersonal = \App\Models\TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            if ($recordatorioPersonal) {
                $data['tipo_evento_id'] = $recordatorioPersonal->id;
            }
        }

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
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = TipoEvento::where('nombre', 'Recordatorio Personal')->first();

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            abort(403, 'No tienes permiso para ver este recordatorio personal.');
        }

        return view('admin.events.show', compact('evento'));
    }

    /**
     * Muestra el formulario para editar un evento
     */
    public function edit(Evento $evento)
    {
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = TipoEvento::where('nombre', 'Recordatorio Personal')->first();

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            abort(403, 'No tienes permiso para editar este recordatorio personal.');
        }

        $tiposEvento = TipoEvento::where('status', true)->get();
        return view('admin.events.edit', compact('evento', 'tiposEvento'));
    }

    /**
     * Actualiza un evento específico (soporta AJAX y formulario)
     */
    public function update(Request $request, Evento $evento)
    {
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = TipoEvento::where('nombre', 'Recordatorio Personal')->first();

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para actualizar este recordatorio personal.'
                ], 403);
            }
            abort(403, 'No tienes permiso para actualizar este recordatorio personal.');
        }

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
                // Si el usuario es alumno, no permitir cambiar el tipo de evento
                if (Auth::user()->hasRole('alumno') && isset($request->tipo_evento_id)) {
                    $recordatorioPersonal = \App\Models\TipoEvento::where('nombre', 'Recordatorio Personal')->first();
                    if ($recordatorioPersonal && $recordatorioPersonal->id != $request->tipo_evento_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No tienes permiso para cambiar el tipo de evento.'
                        ], 403);
                    }
                }

                $evento->update($request->only([
                    'titulo',
                    'descripcion',
                    'fecha_inicio',
                    'fecha_fin',
                    'ubicacion',
                    'url_virtual',
                    'tipo_evento_id',
                    'status'
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado exitosamente',
                    'evento' => $evento
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el evento: ' . $e->getMessage()
                ], 500);
            }
        }

        // Si es una petición de formulario tradicional
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

        // Si el usuario es alumno, forzar tipo de evento a Recordatorio Personal
        if (Auth::user()->hasRole('alumno')) {
            $recordatorioPersonal = \App\Models\TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            if ($recordatorioPersonal) {
                $request->merge(['tipo_evento_id' => $recordatorioPersonal->id]);
            }
        }

        $evento->update($request->all());

        // Sincronizar participantes si se proporcionan
        if ($request->has('participantes')) {
            $evento->participantes()->sync($request->participantes);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento actualizado exitosamente.');
    }

    /**
     * Elimina un evento específico (soporta AJAX y formulario)
     */
    public function destroy(Evento $evento)
    {
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = TipoEvento::where('nombre', 'Recordatorio Personal')->first();

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este recordatorio personal.'
                ], 403);
            }
            abort(403, 'No tienes permiso para eliminar este recordatorio personal.');
        }

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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para editar este recordatorio.'
                ], 403);
            }
            abort(403, 'No tienes permiso para editar este recordatorio.');
        }

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
                    'message' => 'Recordatorio actualizado exitosamente',
                    'evento' => $evento
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el recordatorio: ' . $e->getMessage()
                ], 500);
            }
        }

        // Para peticiones de formulario tradicional
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
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este recordatorio.'
                ], 403);
            }
            abort(403, 'No tienes permiso para eliminar este recordatorio.');
        }

        $evento->delete();

        // Si la petición es AJAX, responde con JSON
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // Petición normal (formulario)
        return redirect()->route('events.calendar')
            ->with('success', 'Recordatorio eliminado exitosamente.');
    }
}
