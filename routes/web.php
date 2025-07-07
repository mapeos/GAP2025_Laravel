<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\EventoParticipanteController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\FacultativoController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\SolicitudCitaController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AlumnoController;

// --------------------------------------------
// Rutas públicas y generales
// --------------------------------------------
Route::get('/', function () {
    return view('landing.landing');
})->name('landing');
// Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::view('astro', 'template.base')->name('astro');

// Ruta temporal para probar modal
Route::get('/test-modal', function () {
    return view('events.test-modal');
})->name('test.modal');

// --------------------------------------------
// Dashboard y páginas de administración
// --------------------------------------------
Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'role:Administrador'])
    ->name('admin.dashboard');
Route::get('/admin/pagina-test', function () {
    return view('admin.dashboard.test');
});
Route::middleware(['auth', 'role:Administrador'])->prefix('admin/pagos')->name('admin.pagos.')->group(function () {
    Route::view('/', 'admin.dashboard.pagos.pagos')->name('index');
    Route::view('/estado', 'admin.dashboard.pagos.estado')->name('estado');
    // Route::view('/facturas', 'admin.dashboard.pagos.facturas')->name('facturas'); // Eliminada para evitar conflicto 404
    Route::get('/facturas/list', [FacturaController::class, 'index'])->name('facturas.list');
    Route::get('/facturas/create', [FacturaController::class, 'create'])->name('facturas.create');
    Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/metodos', [PagoController::class, 'metodos'])->name('metodos');
    Route::post('/metodos', [PagoController::class, 'store'])->name('metodos.store');
});
// Eliminar o comentar la ruta antigua del dashboard genérico
// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// --------------------------------------------
// Perfil de usuario
// --------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';

// --------------------------------------------
// Módulo de Agenda/Calendario y Eventos
// --------------------------------------------
Route::middleware(['auth'])->group(function () {
    // Ruta para el dashboard del profesor
    Route::get('/profesor/home', [\App\Http\Controllers\ProfesorController::class, 'home'])
        ->name('profesor.home')
        ->middleware('role:profesor');

    // Rutas para solicitudes de citas
    Route::get('/solicitud-cita', [\App\Http\Controllers\SolicitudCitaController::class, 'index'])
        ->name('solicitud-cita.index');
    Route::get('/solicitud-cita/recibidas', [\App\Http\Controllers\SolicitudCitaController::class, 'recibidas'])
        ->name('solicitud-cita.recibidas');
    Route::put('/solicitud-cita/{solicitud}/estado', [\App\Http\Controllers\SolicitudCitaController::class, 'ActualizarEstado'])
        ->name('solicitud-cita.actualizar-estado');
    Route::get('/solicitud-citas/{solicitud}/actualizar-estado', [\App\Http\Controllers\SolicitudCitaController::class, 'ActualizarEstado'])
        ->name('solicitud-citas.actualizar-estado');
    Route::post('/solicitud-cita', [\App\Http\Controllers\SolicitudCitaController::class, 'store'])
        ->name('solicitud-cita.store');

    // Rutas para citas con IA
    Route::middleware(['role:Profesor|Administrador'])->group(function () {
        Route::post('/ai/appointments/suggest', [\App\Http\Controllers\AiAppointmentController::class, 'suggest'])
            ->name('ai.appointments.suggest');
        Route::post('/ai/appointments/create', [\App\Http\Controllers\AiAppointmentController::class, 'create'])
            ->name('ai.appointments.create');
    });

    // Rutas para tipos de evento (solo administradores)
    Route::middleware(['role:Administrador'])->group(function () {
        Route::resource('tipos-evento', TipoEventoController::class);
    });
    // Rutas para eventos
    Route::get('calendario', [EventoController::class, 'calendario'])->name('calendario');
    Route::get('eventos/json', [EventoController::class, 'getEventos'])->name('eventos.json');

    // Ruta para que los estudiantes puedan crear recordatorios personales
    Route::post('eventos', [EventoController::class, 'store'])->name('eventos.store');

    // Rutas CRUD de eventos (solo administradores y profesores, excepto store que ya está definido arriba)
    Route::middleware(['role:Administrador|Profesor'])->group(function () {
        Route::resource('eventos', EventoController::class)->except(['store']);
    });
    // Rutas para participantes
    Route::prefix('eventos/{evento}/participantes')->group(function () {
        Route::post('asistencia', [EventoParticipanteController::class, 'updateAsistencia'])->name('eventos.participantes.asistencia');
        Route::post('add', [EventoParticipanteController::class, 'addParticipantes'])->name('eventos.participantes.add');
        Route::post('remove', [EventoParticipanteController::class, 'removeParticipantes'])->name('eventos.participantes.remove');
        Route::post('rol', [EventoParticipanteController::class, 'updateRol'])->name('eventos.participantes.rol');
    });
});

// --------------------------------------------
// Rutas de Cursos
// --------------------------------------------

// Rutas protegidas para cursos
Route::middleware(['auth'])->group(function () {
    // Rutas administrativas de cursos
    Route::prefix('admin/cursos')->name('admin.cursos.')->group(function () {
        Route::get('/', [CursoController::class, 'index'])->name('index');
        Route::get('/create', [CursoController::class, 'create'])->name('create');
        Route::post('/', [CursoController::class, 'store'])->name('store');
        
        // Rutas específicas deben ir ANTES que la ruta genérica {curso}
        Route::get('/{curso}/diploma', [CursoController::class, 'diploma'])->name('diploma');
Route::get('/{curso}/diploma/full', [CursoController::class, 'diplomaFull'])->name('diploma.full');
Route::get('/{curso}/diploma/download', [CursoController::class, 'downloadDiploma'])->name('diploma.download');
        Route::post('/{curso}/upload-portada', [CursoController::class, 'uploadPortada'])->name('upload-portada');
        Route::delete('/{curso}/delete-temario', [CursoController::class, 'deleteTemario'])->name('delete-temario');
        Route::delete('/{curso}/delete-portada', [CursoController::class, 'deletePortada'])->name('delete-portada');
        Route::post('/{curso}/upload', [CursoController::class, 'uploadTemario'])->name('upload');
        Route::post('/{id}/toggle-status', [CursoController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/toggle-estado', [CursoController::class, 'toggleEstado'])->name('toggle-estado');
        Route::post('/{id}/delete', [CursoController::class, 'delete'])->name('delete');
        Route::post('/{id}/restore', [CursoController::class, 'restore'])->name('restore');
        
        // Rutas genéricas van al final
        Route::get('/{curso}', [CursoController::class, 'show'])->name('show');
        Route::get('/{curso}/edit', [CursoController::class, 'edit'])->name('edit');
        Route::put('/{curso}', [CursoController::class, 'update'])->name('update');
    });

    // Rutas públicas de cursos (para ver detalles)
    Route::get('/cursos/{id}', [CursoController::class, 'show'])->name('cursos.show');
});

// Rutas de PARTICIPANTES
Route::middleware(['auth'])->prefix('admin/participantes')->name('admin.participantes.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index'); // Listar participantes
    Route::get('/crear', function () {
        return view('admin.participantes.create');
    })->name('create'); // Formulario de creación de participante
    Route::post('/', [ProfileController::class, 'store'])->name('store'); // Guardar participante
    Route::get('/{persona}', [ProfileController::class, 'showPersona'])->name('show'); // Ver detalles de un participante

    Route::get('/crear', function () {
        return view('admin.participantes.create');
    })->name('create'); // Formulario de creación de participante

});

// Rutas de INSCRIPCIONES a Cursos
Route::prefix('admin/inscripciones')->name('admin.inscripciones.')->middleware(['auth'])->group(function () {
    Route::get('/cursos', [InscripcionController::class, 'cursosActivos'])->name('cursos.activos'); // Lista de cursos activos para inscribir
    Route::get('/cursos/{curso}/inscribir', [InscripcionController::class, 'inscribir'])->name('cursos.inscribir.form'); // Formulario de inscripción
    Route::post('/cursos/{curso}/inscribir', [InscripcionController::class, 'agregarInscripcion'])->name('cursos.inscribir'); // Inscribir personas
    Route::get('/cursos/{curso}/inscritos', [InscripcionController::class, 'verInscritos'])->name('cursos.inscritos'); // Ver inscritos
    Route::delete('/cursos/{curso}/baja/{persona}', [InscripcionController::class, 'darBaja'])->name('cursos.baja'); // Dar de baja a un alumno
    Route::put('/cursos/{curso}/estado/{persona}', [InscripcionController::class, 'actualizarEstado'])->name('cursos.estado'); // Actualizar estado de inscripción
});

// --------------------------------------------
// Administración de usuarios
// --------------------------------------------
Route::prefix('admin/users')->name('admin.users.')->middleware(['auth', 'role:Administrador'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');           // Lista de usuarios
    // Ruta para ver usuarios pendientes de validar (debe ir antes de las rutas con parámetros)
    Route::get('pendent', [UserController::class, 'pendent'])->name('pendent');
    Route::get('/create', [UserController::class, 'create'])->name('create');   // Formulario de creación
    Route::post('/', [UserController::class, 'store'])->name('store');          // Guardar nuevo usuario
    Route::get('/{user}', [UserController::class, 'show'])->name('show');       // Ver detalle (opcional)
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');  // Formulario de edición
    Route::put('/{user}', [UserController::class, 'update'])->name('update');   // Actualizar usuario
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy'); // Eliminar usuario
    Route::post('/{id}/restore', [UserController::class, 'restore'])->name('restore'); // Restaurar usuario eliminado
    // Ruta para validar y asignar rol a usuarios pendientes (bulk)
    Route::post('validate-bulk', [UserController::class, 'validateBulk'])->name('validate.bulk');
    Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggleStatus');
    Route::post('/{id}/change-role', [UserController::class, 'changeRole'])->name('changeRole');
    // Ruta para obtener la persona asociada a un usuario
    Route::get('/users/{user}/persona', [UserController::class, 'getPersonaByUser'])->name('users.persona');
});

//--------------------------------------------
// Rutas de News - CRUD
//--------------------------------------------
Route::get('admin/news', [NewsController::class, 'index'])->name('admin.news.index');
Route::get('admin/news/create', [NewsController::class, 'create'])->name('admin.news.create');
Route::post('admin/news', [NewsController::class, 'store'])->name('admin.news.store');
Route::post('admin/news/{id}/toggle-status', [NewsController::class, 'toggleStatus'])->name('admin.news.toggle-status');
Route::get('admin/news/{news}', [NewsController::class, 'show'])->name('admin.news.show');
Route::get('admin/news/{id}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
Route::put('admin/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
Route::delete('admin/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');
Route::put('admin/news/{id}/restore', [NewsController::class, 'restore'])->name('admin.news.restore');

//--------------------------------------------
// Rutas de Categorias - CRUD
//--------------------------------------------
Route::get('admin/categorias', [CategoriasController::class, 'index'])->name('admin.categorias.index');
Route::get('admin/categorias/create', [CategoriasController::class, 'create'])->name('admin.categorias.create');
Route::post('admin/categorias', [CategoriasController::class, 'store'])->name('admin.categorias.store');
Route::post('admin/categorias/{id}/toggle-status', [CategoriasController::class, 'toggleStatus'])->name('admin.categorias.toggleStatus');
// Route::get('admin/categorias/{categoria}', [CategoriasController::class, 'show'])->name('admin.categorias.show'); opcional si quieres mostrar una categoría
Route::get('admin/categorias/{categoria}/edit', [CategoriasController::class, 'edit'])->name('admin.categorias.edit');
Route::put('admin/categorias/{categoria}', [CategoriasController::class, 'update'])->name('admin.categorias.update');
Route::delete('admin/categorias/{categoria}', [CategoriasController::class, 'destroy'])->name('admin.categorias.destroy');
Route::put('admin/categorias/{id}/restore', [CategoriasController::class, 'restore'])->name('admin.categorias.restore');

//--------------------------------------------
// Rutas para usuarios pendientes de validar y roles específicos
//--------------------------------------------
Route::get('/pendiente/home', [UserController::class, 'homePendiente'])->name('pendiente.home');
// Rutas para el rol Alumno
Route::middleware(['auth', 'role:Alumno'])->group(function () {
    Route::get('/alumno/home', [AlumnoController::class, 'home'])->name('alumno.home');
});
// Rutas para el rol Profesor
Route::middleware(['auth', 'role:Profesor'])->group(function () {
    Route::view('/profesor/home', 'profesor.home')->name('profesor.home');
});

//--------------------------------------------
// Rutas de Events - CRUD (legacy o duplicadas, revisar si se usan)
//--------------------------------------------
Route::prefix('admin/events')->name('admin.events.')->middleware(['auth', 'role:Administrador|Profesor'])->group(function () {
    // Rutas para tipos de evento (solo administradores)
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/types', [TipoEventoController::class, 'index'])->name('types.index');
        Route::get('/types/create', [TipoEventoController::class, 'create'])->name('types.create');
        Route::post('/types', [TipoEventoController::class, 'store'])->name('types.store');
        Route::get('/types/{tipoEvento}/edit', [TipoEventoController::class, 'edit'])->name('types.edit');
        Route::put('/types/{tipoEvento}', [TipoEventoController::class, 'update'])->name('types.update');
        Route::delete('/types/{tipoEvento}', [TipoEventoController::class, 'destroy'])->name('types.destroy');
    });
    // Rutas CRUD de eventos
    Route::get('/', [EventoController::class, 'index'])->name('index');
    Route::get('/create', [EventoController::class, 'create'])->name('create');
    Route::post('/', [EventoController::class, 'store'])->name('store');
    Route::get('/{evento}', [EventoController::class, 'show'])->name('show');
    Route::get('/{evento}/edit', [EventoController::class, 'edit'])->name('edit');
    Route::put('/{evento}', [EventoController::class, 'update'])->name('update');
    Route::delete('/{evento}', [EventoController::class, 'destroy'])->name('destroy');
    // Rutas para participantes
    Route::prefix('{evento}/participants')->name('participants.')->group(function () {
        Route::post('/attendance', [EventoParticipanteController::class, 'updateAsistencia'])->name('attendance');
        Route::post('/add', [EventoParticipanteController::class, 'addParticipantes'])->name('add');
        Route::post('/remove', [EventoParticipanteController::class, 'removeParticipantes'])->name('remove');
        Route::post('/role', [EventoParticipanteController::class, 'updateRol'])->name('role');
    });
});

//--------------------------------------------
// Rutas para usuarios autenticados (incluyendo alumnos)
//--------------------------------------------
Route::prefix('events')->name('events.')->middleware(['auth'])->group(function () {
    // Rutas comunes para todos los usuarios autenticados
    Route::get('/calendar', [EventoController::class, 'calendario'])->name('calendar');
    Route::get('/json', [EventoController::class, 'getEventos'])->name('json');
    Route::get('/{evento}', [EventoController::class, 'show'])->name('show');
    // Rutas específicas para alumnos (recordatorios personales)
    Route::middleware(['role:Alumno'])->group(function () {
        Route::get('/reminders/create', [EventoController::class, 'createReminder'])->name('reminders.create');
        Route::post('/reminders', [EventoController::class, 'storeReminder'])->name('reminders.store');
        Route::get('/reminders/{evento}/edit', [EventoController::class, 'editReminder'])->name('reminders.edit');
        Route::put('/reminders/{evento}', [EventoController::class, 'updateReminder'])->name('reminders.update');
        Route::delete('/reminders/{evento}', [EventoController::class, 'destroyReminder'])->name('reminders.destroy');
    });
});

// Rutas de detalle de curso para alumnos
Route::middleware(['auth', 'role:Alumno'])->group(function () {
    Route::get('/alumno/cursos/{id}', function($id) {
        $curso = \App\Models\Curso::findOrFail($id);
        return view('alumno.cursos.show', compact('curso'));
    })->name('alumno.cursos.show');
    Route::get('/alumno/cursos/{id}/inscribir', function($id) {
        $curso = \App\Models\Curso::findOrFail($id);
        return view('alumno.cursos.inscribir', compact('curso'));
    })->name('alumno.cursos.inscribir');
    Route::post('/alumno/cursos/{id}/inscribir', function($id) {
        $curso = \App\Models\Curso::findOrFail($id);
        $user = Auth::user();
        $persona = $user->persona;
        // Validar que no esté ya inscrito
        if ($persona && !$persona->cursos()->where('cursos.id', $curso->id)->exists()) {
            $persona->cursos()->attach($curso->id, [
                'rol_participacion_id' => 1, // 1 = alumno
                'estado' => 'pendiente',
            ]);
            return redirect()->route('alumno.cursos.show', $curso->id)->with('success', 'Solicitud de inscripción enviada.');
        }
        return redirect()->route('alumno.cursos.show', $curso->id)->with('error', 'Ya tienes una inscripción pendiente o activa en este curso.');
    })->name('alumno.cursos.inscribir.solicitar');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:Administrador'])
    ->group(function () {
        Route::get('notificaciones', [NotificationController::class, 'index'])->name('notificaciones.index');
        Route::get('notificaciones/create', [NotificationController::class, 'create'])->name('notificaciones.create');
        Route::post('notificaciones', [NotificationController::class, 'store'])->name('notificaciones.store');

        // WhatsApp
        Route::get('whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'showForm'])->name('whatsapp.form');
        Route::post('whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'send'])->name('whatsapp.send');
    });

//--------------------------------------------
// Rutas para sugerencias de IA
//--------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::post('/ai/appointment-suggestions', [\App\Http\Controllers\AiAppointmentController::class, 'suggestAppointments'])
        ->name('ai.appointment-suggestions');

    // Rutas para el modal del profesor
    Route::middleware(['role:Profesor'])->group(function () {
        Route::post('/ai/professor/suggestions', [\App\Http\Controllers\AiAppointmentController::class, 'suggestForProfessor'])
            ->name('ai.professor.suggestions');
        Route::post('/ai/professor/create-appointment', [\App\Http\Controllers\AiAppointmentController::class, 'createAppointment'])
            ->name('ai.professor.create-appointment');
    });
});

//--------------------------------------------
// Rutas para sugerencias de IA (duplicadas - mantener por compatibilidad)
//--------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::post('/ai/appointment-suggestions', [\App\Http\Controllers\AiAppointmentController::class, 'suggestAppointments'])
        ->name('ai.appointment-suggestions');
});

// Rutas de autenticación con Firebase
Route::post('/login/firebase', [FirebaseAuthController::class, 'login']);


Route::get('/admin/sincronizar-usuarios-personas', [\App\Http\Controllers\UserController::class, 'sincronizarUsuariosPersonas']);

// Rutas para la gestión de solicitudes de inscripción para el administrador
Route::middleware(['auth', 'role:Administrador'])->prefix('admin/solicitudes')->name('admin.solicitudes.')->group(function () {
    Route::get('/', function() {
        // Obtener todas las solicitudes de inscripción (pivot estado = pendiente, espera, activo, rechazado)
        $solicitudes = \App\Models\Persona::whereHas('cursos', function($q) {
            $q->whereIn('participacion.estado', ['pendiente', 'espera', 'activo', 'rechazado']);
        })->with(['cursos' => function($q) {
            $q->withPivot('estado');
        }])
        ->get()
        ->flatMap(function($persona) {
            return $persona->cursos->map(function($curso) use ($persona) {
                $curso->pivot->persona = $persona;
                $curso->curso = $curso; // Asignar el curso a sí mismo para la vista
                return $curso;
            });
        })
        ->sortByDesc(function($solicitud) {
            return $solicitud->pivot->created_at ?? $solicitud->created_at;
        })
        ->values();

        // Convertir a LengthAwarePaginator manualmente para paginar colecciones
        $perPage = 10;
        $page = request()->input('page', 1);
        $paginator = new Illuminate\Pagination\LengthAwarePaginator(
            $solicitudes->forPage($page, $perPage),
            $solicitudes->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        if (request()->ajax()) {
            return view('admin.solicitudes._tabla_paginada', ['solicitudes' => $paginator]);
        }
        return view('admin.solicitudes.index', ['solicitudes' => $paginator]);
    })->name('index');
    Route::put('/{curso}/{persona}', function($cursoId, $personaId) {
        $persona = \App\Models\Persona::findOrFail($personaId);
        $curso = \App\Models\Curso::findOrFail($cursoId);
        $estado = request('estado');
        $persona->cursos()->updateExistingPivot($curso->id, ['estado' => $estado]);
        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    })->name('update');
});

// Chat entre usuarios (alumnos y profesores)
Route::middleware(['auth'])->prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ChatController::class, 'index'])->name('index');
    // Route::get('/{id}', ...) eliminada, todo se gestiona en index
    Route::post('/{id}', [\App\Http\Controllers\ChatController::class, 'store'])->name('store');
    Route::get('/search/users', [\App\Http\Controllers\ChatController::class, 'searchUsers'])->name('searchUsers');
    Route::post('/hide/{otherUserId}', [\App\Http\Controllers\ChatHiddenController::class, 'hide'])->name('hide');
    Route::post('/unhide/{otherUserId}', [\App\Http\Controllers\ChatHiddenController::class, 'unhide'])->name('unhide');
    Route::get('/{id}', function($id, \App\Application\Chat\GetMessagesBetweenUsers $getMessages, \App\Application\Chat\MarkMessagesAsRead $markAsRead) {
        // Solo responder si es AJAX
        if (!request()->ajax() && !request('ajax')) {
            abort(404);
        }
        return app(\App\Http\Controllers\ChatController::class)->show($id, $getMessages, $markAsRead);
    })->where('id', '[0-9]+');
});

// Rutas para Facultativo (médicos)
Route::middleware(['auth', 'role:Facultativo|Administrador'])->prefix('facultativo')->name('facultativo.')->group(function () {
    Route::get('/', [FacultativoController::class, 'index'])->name('home');
    Route::get('/citas', [FacultativoController::class, 'citas'])->name('citas');
    Route::get('/citas/confirmadas', [FacultativoController::class, 'citasConfirmadas'])->name('citas.confirmadas');
    Route::get('/citas/pendientes', [FacultativoController::class, 'citasPendientes'])->name('citas.pendientes');
    
    // Rutas específicas de citas (deben ir antes que las rutas con parámetros)
    Route::get('/cita/{id}/edit', [FacultativoController::class, 'editCita'])->name('cita.edit');
    Route::get('/cita/{id?}', [FacultativoController::class, 'cita'])->name('cita');
    
    Route::get('/pacientes', [FacultativoController::class, 'pacientes'])->name('pacientes');
    Route::get('/paciente', [FacultativoController::class, 'paciente'])->name('paciente');
    Route::get('/tratamientos', [FacultativoController::class, 'tratamientos'])->name('tratamientos');
    
    // Rutas específicas de tratamientos (deben ir antes que las rutas con parámetros)
    Route::get('/tratamiento/new', [FacultativoController::class, 'newTratamiento'])->name('tratamiento.new');
    Route::get('/tratamiento/{id}/edit', [FacultativoController::class, 'editTratamiento'])->name('tratamiento.edit');
    Route::get('/tratamiento/{id}', [FacultativoController::class, 'tratamiento'])->name('tratamiento');
    
    // Rutas POST/PUT para formularios
    Route::post('/tratamiento', [FacultativoController::class, 'storeTratamiento'])->name('tratamiento.store');
    Route::put('/tratamiento/{id}', [FacultativoController::class, 'updateTratamiento'])->name('tratamiento.update');
    Route::delete('/tratamiento/{id}', [FacultativoController::class, 'destroyTratamiento'])->name('tratamiento.destroy');
    Route::post('/cita', [FacultativoController::class, 'storeCita'])->name('cita.store');
    Route::put('/cita/{id}', [FacultativoController::class, 'updateCita'])->name('cita.update');
    
    // Ruta para actualizar estado de citas médicas
    Route::put('/citas/{solicitud}/actualizar-estado', [SolicitudCitaController::class, 'ActualizarEstado'])
        ->name('citas.actualizar-estado');
});

