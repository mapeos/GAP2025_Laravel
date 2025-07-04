<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EventoController extends Controller
{
    /**
     * Muestra una lista de eventos
     */
    public function index()
    {
        // Optimizar consulta con eager loading y select específico
        $eventos = Cache::remember('eventos.index', 300, function () {
            return Evento::with(['tipoEvento:id,nombre,color', 'participantes:id,name'])
                ->select(['id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'tipo_evento_id', 'creado_por', 'ubicacion', 'url_virtual', 'status'])
                ->where('status', true)
                ->orderBy('fecha_inicio', 'desc')
                ->get();
        });

        return view('admin.events.index', compact('eventos'));
    }

    /**
     * Muestra el calendario de eventos
     */
    public function calendario()
    {
        $userId = Auth::id();

        // Cache de tipos de evento
        $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
            return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
        });

        // Cache de usuarios para modales
        $profesores = Cache::remember('profesores.calendar', 1800, function () {
            return User::whereHas('roles', function($query) {
                $query->where('name', 'profesor');
            })->select(['id', 'name', 'email'])->get();
        });

        $alumnos = Cache::remember('alumnos.calendar', 1800, function () {
            return User::whereHas('roles', function($query) {
                $query->where('name', 'alumno');
            })->where('status', 'activo')->select(['id', 'name', 'email'])->get();
        });

        // Los eventos se cargarán vía AJAX para mejor rendimiento
        return view('events.calendar', compact('profesores', 'alumnos'));
    }

    /**
     * Obtiene los eventos para el calendario en formato JSON (AJAX)
     */
    public function getEventos()
    {
        $userId = Auth::id();

        // Cache de tipos de evento
        $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
            return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
        });

        // Optimizar consulta con eager loading y select específico
        $query = Evento::with(['tipoEvento:id,nombre,color', 'participantes:id,name,email'])
            ->select(['id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'tipo_evento_id', 'creado_por', 'ubicacion', 'url_virtual'])
            ->where('status', true);

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

        // Cache de eventos por usuario
        $cacheKey = "eventos.user.{$userId}";
        $eventos = Cache::remember($cacheKey, 300, function () use ($query) {
            return $query->get()
                ->map(function ($evento) {
                    return [
                        'id' => $evento->id,
                        'title' => $evento->titulo,
                        'start' => $evento->fecha_inicio,
                        'end' => $evento->fecha_fin,
                        'color' => $evento->tipoEvento->color,
                        'descripcion' => $evento->descripcion,
                        'tipo_evento_nombre' => $evento->tipoEvento->nombre,
                        'tipo_evento_id' => $evento->tipo_evento_id,
                        'creado_por' => $evento->creado_por,
                        'ubicacion' => $evento->ubicacion,
                        'url_virtual' => $evento->url_virtual,
                        'participantes' => $evento->participantes,
                        'url' => route('events.show', $evento->id)
                    ];
                });
        });

        return response()->json($eventos);
    }

    /**
     * Muestra el formulario para crear un nuevo evento
     */
    public function create()
    {
        // Cache de tipos de evento
        $tiposEvento = Cache::remember('tipos_evento.active', 3600, function () {
            return TipoEvento::where('status', true)->select(['id', 'nombre', 'color'])->get();
        });

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

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['creado_por'] = Auth::id();

            // Verificar si el usuario es alumno y forzar tipo de evento a Recordatorio Personal
            if (Auth::user()->hasRole('alumno')) {
                $recordatorioPersonal = Cache::remember('tipo_recordatorio', 3600, function () {
                    return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
                });
                if ($recordatorioPersonal) {
                    $data['tipo_evento_id'] = $recordatorioPersonal->id;
                }
            }

            $evento = Evento::create($data);

            // Sincronizar participantes si se proporcionan
            if ($request->has('participantes')) {
                $evento->participantes()->attach($request->participantes);
            }

            // Limpiar cache
            $this->clearEventosCache();

            DB::commit();

            // Si la petición es AJAX, responde con JSON
            if ($request->expectsJson()) {
                // Cargar la relación tipoEvento para obtener el color
                $evento->load('tipoEvento:id,nombre,color');

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

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear evento: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al crear evento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra un evento específico
     */
    public function show(Evento $evento)
    {
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
            return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
        });

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            abort(403, 'No tienes permiso para ver este recordatorio personal.');
        }

        // Cargar relaciones necesarias
        $evento->load(['tipoEvento:id,nombre,color', 'participantes:id,name,email']);

        return view('admin.events.show', compact('evento'));
    }

    /**
     * Muestra el formulario para editar un evento
     */
    public function edit(Evento $evento)
    {
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
            return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
        });

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            abort(403, 'No tienes permiso para editar este recordatorio personal.');
        }

        // Cache de tipos de evento
        $tiposEvento = Cache::remember('tipos_evento.active', 3600, function () {
            return TipoEvento::where('status', true)->select(['id', 'nombre', 'color'])->get();
        });

        return view('admin.events.edit', compact('evento', 'tiposEvento'));
    }

    /**
     * Actualiza un evento específico (soporta AJAX y formulario)
     */
    public function update(Request $request, Evento $evento)
    {
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
            return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
        });

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para editar este recordatorio personal.'
                ], 403);
            }
            abort(403, 'No tienes permiso para editar este recordatorio personal.');
        }

        // Si el usuario es estudiante, no puede cambiar el tipo de evento
        if (Auth::user()->hasRole('Alumno') && $request->has('tipo_evento_id') && $request->tipo_evento_id != $evento->tipo_evento_id) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para cambiar el tipo de evento.'
                ], 403);
            }
            return redirect()->back()
                ->with('error', 'No tienes permiso para cambiar el tipo de evento.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Actualizar datos básicos del evento
            $evento->fill($request->all());
            $evento->save();

            // Sincronizar participantes si se proporcionan
            if ($request->has('participantes')) {
                $participantes = $request->participantes;
                $evento->participantes()->sync($participantes);
            }

            // Limpiar cache
            $this->clearEventosCache();

            DB::commit();

            // Si la petición es AJAX, responde con JSON
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Evento actualizado exitosamente',
                    'evento' => $evento
                ]);
            }

            // Petición normal (formulario)
            return redirect()->route('events.show', $evento->id)
                ->with('success', 'Evento actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el evento: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar evento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina un evento específico (soporta AJAX y formulario)
     */
    public function destroy(Evento $evento)
    {
        // Verificar si es un recordatorio personal y si el usuario actual es el creador
        $tipoRecordatorio = Cache::remember('tipo_recordatorio', 3600, function () {
            return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
        });

        if ($tipoRecordatorio && $evento->tipo_evento_id == $tipoRecordatorio->id && $evento->creado_por != Auth::id()) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este recordatorio personal.'
                ], 403);
            }
            abort(403, 'No tienes permiso para eliminar este recordatorio personal.');
        }

        DB::beginTransaction();
        try {
            // Eliminar participantes primero
            $evento->participantes()->detach();

            // Eliminar evento
            $evento->delete();

            // Limpiar cache
            $this->clearEventosCache();

            DB::commit();

            // Si la petición es AJAX, responde con JSON
            if (request()->expectsJson() || request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Evento eliminado exitosamente']);
            }

            // Petición normal (formulario)
            return redirect()->route('admin.events.index')
                ->with('success', 'Evento eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();

            if (request()->expectsJson() || request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar evento: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar evento: ' . $e->getMessage());
        }
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

        DB::beginTransaction();
        try {
            $recordatorioPersonal = Cache::remember('tipo_recordatorio', 3600, function () {
                return TipoEvento::where('nombre', 'Recordatorio Personal')->first();
            });

            $evento = Evento::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'tipo_evento_id' => $recordatorioPersonal->id,
                'creado_por' => Auth::id(),
                'status' => true,
            ]);

            // Añadir al usuario como participante
            $evento->participantes()->attach(Auth::id(), [
                'rol' => 'Creador',
                'estado_asistencia' => 'confirmado',
                'status' => true,
            ]);

            // Limpiar cache
            $this->clearEventosCache();

            DB::commit();

            return redirect()->route('events.calendar')
                ->with('success', 'Recordatorio creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error al crear recordatorio: ' . $e->getMessage())
                ->withInput();
        }
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

        DB::beginTransaction();
        try {
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

                $evento->update($request->only([
                    'titulo',
                    'descripcion',
                    'fecha_inicio',
                    'fecha_fin'
                ]));

                // Limpiar cache
                $this->clearEventosCache();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Recordatorio actualizado exitosamente',
                    'evento' => $evento
                ]);
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

            // Limpiar cache
            $this->clearEventosCache();

            DB::commit();

            return redirect()->route('events.calendar')
                ->with('success', 'Recordatorio actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el recordatorio: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar recordatorio: ' . $e->getMessage())
                ->withInput();
        }
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

        DB::beginTransaction();
        try {
            $evento->delete();

            // Limpiar cache
            $this->clearEventosCache();

            DB::commit();

            // Si la petición es AJAX, responde con JSON
            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            // Petición normal (formulario)
            return redirect()->route('events.calendar')
                ->with('success', 'Recordatorio eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar recordatorio: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('events.calendar')
                ->with('error', 'Error al eliminar recordatorio: ' . $e->getMessage());
        }
    }

    /**
     * Limpia el cache de eventos
     */
    private function clearEventosCache()
    {
        Cache::forget('eventos.index');

        // Optimización: Limpiar solo la caché del usuario actual en lugar de todos los usuarios
        $userId = Auth::id();
        if ($userId) {
            Cache::forget("eventos.user.{$userId}");
            Cache::forget("eventos.api.user.{$userId}");
            Cache::forget('profesores.calendar');
            Cache::forget('alumnos.calendar');
        } else {
            // Fallback: limpiar cache de eventos por usuario solo si es necesario
            $users = DB::table('users')->pluck('id');
            foreach ($users as $userId) {
                Cache::forget("eventos.user.{$userId}");
                Cache::forget("eventos.api.user.{$userId}");
            }
            Cache::forget('profesores.calendar');
            Cache::forget('alumnos.calendar');
        }
    }
}
