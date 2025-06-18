@extends('template.base')

@section('content')
<style>
    #calendar {
        margin: 20px 0;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .fc-event {
        cursor: pointer;
    }
    .fc-event-title {
        font-weight: 500;
    }
    .fc-daygrid-event {
        padding: 2px 4px;
    }
    .fc-daygrid-day-number {
        font-size: 0.9em;
        padding: 4px;
    }
    .fc-toolbar-title {
        font-size: 1.5em !important;
        font-weight: 500;
    }
    .fc-button-primary {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
    }
    .fc-button-primary:hover {
        background-color: #0b5ed7 !important;
        border-color: #0a58ca !important;
    }
    .fc-button-primary:not(:disabled):active,
    .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #0a58ca !important;
        border-color: #0a53be !important;
    }
    /* Estilos para la vista de agenda */
    #agendaView .list-group-item.list-group-item-secondary {
        transition: background-color 0.2s;
    }
    #agendaView .list-group-item.list-group-item-secondary:hover {
        background-color: #6c757d;
        color: white;
    }
    #agendaView .events-container {
        transition: max-height 0.3s ease-in-out;
        overflow: hidden;
    }
    #agendaView .events-container.d-none {
        max-height: 0;
    }
    #agendaView .events-container:not(.d-none) {
        max-height: 1000px;
    }
</style>

<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    <div class="d-flex gap-2 mb-3">
        @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearEventoModal">
                Crear nuevo evento
            </button>
        @else
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearEventoModal">
                Crear recordatorio personal
            </button>
        @endif

        @if(Auth::user()->hasRole('Alumno'))
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaModal">
                Solicitar cita/consulta con profesor
            </button>
        @else
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaModal2">
                Agendar cita/consulta con Alumno
            </button>
        @endif
        @if(Auth::user()->hasRole('profesor'))
            <a href="{{ route('solicitud-cita.recibidas') }}" class="btn btn-info">
                <i class="ri-mail-line"></i> Ver solicitudes recibidas
            </a>
        @else
            <a href="{{ route('solicitud-cita.index') }}" class="btn btn-info">
                <i class="ri-mail-line"></i> Ver mis solicitudes
            </a>
        @endif

        <button id="btnAgendaView" class="btn btn-secondary">
            <i class="ri-list-check-line"></i> Ver agenda
        </button>
    </div>

    <div class="modal fade" id="solicitudCitaModal" tabindex="-1" aria-labelledby="solicitudCitaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('solicitud-cita.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="solicitudCitaModalLabel">Solicitar cita/consulta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="profesor_id" class="form-label">Profesor</label>
                            <select class="form-select" name="profesor_id" required>
                                <option value="">Seleccione un profesor</option>
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->id }}">{{ $profesor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <input type="text" class="form-control" name="motivo" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_propuesta" class="form-label">Fecha y hora propuesta</label>
                            <input type="datetime-local" class="form-control" name="fecha_propuesta" required
                                   min="{{ date('Y-m-d\TH:i') }}"
                                   step="1800">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="solicitudCitaModal2" tabindex="-1" aria-labelledby="solicitudCitaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('solicitud-cita.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="solicitudCitaModalLabel">Agendar cita/consulta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="alumno_id" class="form-label">Alumno</label>
                            <select class="form-select" name="alumno_id" required>
                                <option value="">Seleccione un Alumno</option>
                                @foreach($alumnos as $Alumno)
                                    <option value="{{ $Alumno->id }}">{{ $Alumno->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <input type="text" class="form-control" name="motivo" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_propuesta" class="form-label">Fecha y hora propuesta</label>
                            <input type="datetime-local" class="form-control" name="fecha_propuesta" required
                                   min="{{ date('Y-m-d\TH:i') }}"
                                   step="1800">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="calendar"></div>

    <!-- Contenedor para la vista de agenda -->
    <div id="agendaView" class="d-none">
        <div class="card">
            <div class="card-header bg-secondary text-white text-center py-3">
                <h5 class="mb-0">Agenda de Eventos</h5>
            </div>
            <div class="card-body">
                <div id="agendaList" class="list-group">
                    <!-- Los eventos se cargarán aquí dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear nuevo evento -->
    <div class="modal fade" id="crearEventoModal" tabindex="-1" aria-labelledby="crearEventoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    @if(Auth::user()->hasRole('Alumno'))
                        <h5 class="modal-title" id="crearEventoModalLabel">Crear recordatorio personal</h5>
                    @else
                        <h5 class="modal-title" id="crearEventoModalLabel">Crear nuevo evento</h5>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearEvento" novalidate>
                        <div class="mb-3">
                            <label for="nuevoTitulo" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nuevoTitulo" required
                                   maxlength="100" minlength="3"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="El título debe tener entre 3 y 100 caracteres">
                            <div class="invalid-feedback">Por favor ingrese un título válido (3-100 caracteres)</div>
                        </div>
                        <div class="mb-3">
                            <label for="nuevaDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="nuevaDescripcion" maxlength="500" rows="3"></textarea>
                            <div class="form-text">Máximo 500 caracteres</div>
                        </div>
                        @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
                        <div class="mb-3">
                            <label for="nuevaUbicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="nuevaUbicacion" maxlength="200">
                        </div>
                        <div class="mb-3">
                            <label for="nuevaUrlVirtual" class="form-label">URL Virtual</label>
                            <input type="url" class="form-control" id="nuevaUrlVirtual"
                                   pattern="https?://.+"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="Debe ser una URL válida comenzando con http:// o https://">
                            <div class="invalid-feedback">Por favor ingrese una URL válida</div>
                        </div>
                        <div class="mb-3">
                            <label for="nuevoTipoEvento" class="form-label">Tipo de evento <span class="text-danger">*</span></label>
                            <select class="form-select" id="nuevoTipoEvento" required>
                                <option value="">Seleccione un tipo</option>
                                @foreach(\App\Models\TipoEvento::where('status', true)->get() as $tipo)
                                    <option value="{{ $tipo->id }}" data-color="{{ $tipo->color }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Por favor seleccione un tipo de evento</div>
                        </div>
                        @else
                        <input type="hidden" id="nuevaUbicacion" value="">
                        <input type="hidden" id="nuevaUrlVirtual" value="">
                        <div class="mb-3">
                            <label for="nuevoTipoEvento" class="form-label">Tipo de evento</label>
                            @php
                                $tipoRecordatorio = \App\Models\TipoEvento::where('status', true)
                                    ->where('nombre', 'Recordatorio Personal')
                                    ->first();
                            @endphp
                            @if($tipoRecordatorio)
                                <input type="hidden" id="nuevoTipoEvento" value="{{ $tipoRecordatorio->id }}" data-color="{{ $tipoRecordatorio->color }}" required>
                                <input type="text" class="form-control" value="{{ $tipoRecordatorio->nombre }}" readonly>
                            @else
                                <div class="alert alert-danger">No se encontró el tipo de evento "Recordatorio Personal"</div>
                            @endif
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nuevaFechaInicio" class="form-label">Fecha inicio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="nuevaFechaInicio" required
                                           min="{{ date('Y-m-d') }}"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="La fecha debe ser hoy o posterior">
                                    <select class="form-select" id="nuevaHoraInicio" style="max-width: 120px;" required>
                                        @php
                                            $horas = [];
                                            for($hora = 12; $hora < 21; $hora++) {
                                                for($minuto = 0; $minuto < 60; $minuto += 15) {
                                                    $horas[] = sprintf('%02d:%02d', $hora, $minuto);
                                                }
                                            }
                                        @endphp
                                        @foreach($horas as $hora)
                                            <option value="{{ $hora }}">{{ $hora }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback">Por favor seleccione una fecha y hora de inicio válida</div>
                                <input type="hidden" id="nuevaFechaInicioCompleta">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nuevaFechaFin" class="form-label">Fecha fin <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="nuevaFechaFin" required
                                           min="{{ date('Y-m-d') }}"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="La fecha debe ser hoy o posterior">
                                    <select class="form-select" id="nuevaHoraFin" style="max-width: 120px;" required>
                                        @foreach($horas as $hora)
                                            <option value="{{ $hora }}">{{ $hora }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback">Por favor seleccione una fecha y hora de fin válida</div>
                                <input type="hidden" id="nuevaFechaFinCompleta">
                            </div>
                        </div>
                        <div id="errorContainer" class="alert alert-danger d-none"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnCrearEvento" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Crear evento
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar evento -->
    <div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header d-flex align-items-start justify-content-between">
            <div>
              <h5 class="modal-title" id="eventoModalLabel">Detalles del evento</h5>
            </div>
            <div class="d-flex gap-2">
              <button type="button" id="btnEditarEvento" class="btn btn-sm btn-outline-secondary" title="Editar">
                <i class="ri-pencil-line"></i>
              </button>
              <button type="button" id="btnEliminar" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i class="ri-delete-bin-line"></i>
              </button>
              <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
          </div>
          <div class="modal-body">
            <form id="formEditarEvento">
              <input type="hidden" id="eventoId">
              <input type="hidden" id="eventoTipoId">
              <input type="hidden" id="eventoCreadoPor">

              <div class="row">
                <div class="col-md-12 mb-2" id="grupoTitulo">
                  <label for="titulo" class="form-label">Título</label>
                  <input type="text" class="form-control" id="titulo" required disabled>
                </div>
              </div>

              <div class="row" id="grupoDescripcion" style="display:none;">
                <div class="col-md-12 mb-2">
                  <label for="descripcion" class="form-label">Descripción</label>
                  <textarea class="form-control" id="descripcion" rows="2" disabled></textarea>
                </div>
              </div>

              <div class="row" id="grupoTipoEvento" style="display:none;">
                <div class="col-md-6 mb-2">
                  <label class="form-label">Tipo de evento</label>
                  <p id="tipoEventoNombre" class="form-control-plaintext small"></p>
                </div>
              </div>
              <div class="row" id="grupoCreadoPor" style="display:none;">
                <div class="col-md-6 mb-2">
                  <label class="form-label">Creado por</label>
                  <p id="creadoPorNombre" class="form-control-plaintext small"></p>
                </div>
              </div>

              <div class="row" id="grupoUbicacion" style="display:none;">
                <div class="col-md-6 mb-2">
                  <label class="form-label">Ubicación</label>
                  <p id="ubicacion" class="form-control-plaintext small"></p>
                </div>
              </div>
              <div class="row" id="grupoUrlVirtual" style="display:none;">
                <div class="col-md-6 mb-2">
                  <label class="form-label">URL Virtual</label>
                  <p id="urlVirtual" class="form-control-plaintext small"></p>
                </div>
              </div>

              <div class="row" id="grupoFechaCreacion" style="display:none;">
                <div class="col-md-4 mb-2">
                  <label class="form-label">Fecha de creación</label>
                  <p id="fechaCreacion" class="form-control-plaintext small"></p>
                </div>
              </div>
              <div class="row" id="grupoFechaInicio" style="display:none;">
                <div class="col-md-4 mb-2">
                  <label class="form-label">Fecha de inicio</label>
                  <p id="fechaInicio" class="form-control-plaintext small"></p>
                </div>
              </div>
              <div class="row" id="grupoFechaFin" style="display:none;">
                <div class="col-md-4 mb-2">
                  <label class="form-label">Fecha de fin</label>
                  <p id="fechaFin" class="form-control-plaintext small"></p>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnGuardar" class="btn btn-primary" style="display: none;">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Botón flotante para crear evento -->
    <!-- Este btón abre el modal de creación de eventos al hacer click -->
    <button id="fabCrearEvento" class="btn btn-primary rounded-circle"
            style="position: fixed; bottom: 32px; right: 32px; z-index: 1050; width: 56px; height: 56px; font-size: 2rem;" >
        +
    </button>

    <style>
        /* Estilos para el calendario */
        #calendar {
            margin-bottom: 20px;
        }

        /* Estilos para el botón flotante */
        .floating-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .floating-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .floating-button i {
            font-size: 24px;
        }

        /* Estilos para la vista de agenda */
        #agendaView {
            margin-bottom: 20px;
        }

        #agendaList .list-group-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        #agendaList .list-group-item:not(.list-group-item-secondary):hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        #agendaList .badge {
            font-weight: normal;
        }
    </style>
    <script>
        // Variables de usuario para JS
        const userRole = "{{ Auth::user()->getRoleNames()->first() }}";
        const userId = {{ Auth::id() }};

        document.addEventListener('DOMContentLoaded', function() {
            // Función utilitaria para formato fechas en español
            function formatearFecha(fecha) {
                return fecha.toLocaleDateString('es-ES', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Funciones para manejar fecha y hora
            function combinarFechaHora(fecha, hora) {
                return `${fecha}T${hora}`;
            }

            function separarFechaHora(fechaHora) {
                const [fecha, hora] = fechaHora.split('T');
                return { fecha, hora };
            }

            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Función para validar el formulario
            function validarFormulario() {
                const form = document.getElementById('formCrearEvento');
                const titulo = document.getElementById('nuevoTitulo').value;
                const tipoEvento = document.getElementById('nuevoTipoEvento').value;
                const fechaInicio = document.getElementById('nuevaFechaInicio').value;
                const fechaFin = document.getElementById('nuevaFechaFin').value;
                const horaInicio = document.getElementById('nuevaHoraInicio').value;
                const horaFin = document.getElementById('nuevaHoraFin').value;
                const urlVirtual = document.getElementById('nuevaUrlVirtual').value;

                let isValid = true;
                const errorContainer = document.getElementById('errorContainer');
                errorContainer.classList.add('d-none');
                errorContainer.innerHTML = '';

                // Validar título
                if (!titulo || titulo.length < 3 || titulo.length > 100) {
                    document.getElementById('nuevoTitulo').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('nuevoTitulo').classList.remove('is-invalid');
                }

                // Validar tipo de evento
                if (!tipoEvento) {
                    document.getElementById('nuevoTipoEvento').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('nuevoTipoEvento').classList.remove('is-invalid');
                }

                // Validar fechas
                if (!fechaInicio || !fechaFin) {
                    document.getElementById('nuevaFechaInicio').classList.add('is-invalid');
                    document.getElementById('nuevaFechaFin').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('nuevaFechaInicio').classList.remove('is-invalid');
                    document.getElementById('nuevaFechaFin').classList.remove('is-invalid');
                }

                // Validar URL virtual si existe
                if (urlVirtual && !urlVirtual.match(/^https?:\/\/.+/)) {
                    document.getElementById('nuevaUrlVirtual').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('nuevaUrlVirtual').classList.remove('is-invalid');
                }

                // Validar que la fecha de fin sea posterior a la de inicio
                if (fechaInicio && fechaFin) {
                    const fechaInicioCompleta = new Date(`${fechaInicio}T${horaInicio}`);
                    const fechaFinCompleta = new Date(`${fechaFin}T${horaFin}`);

                    if (fechaFinCompleta <= fechaInicioCompleta) {
                        errorContainer.classList.remove('d-none');
                        errorContainer.innerHTML = 'La fecha y hora de fin debe ser posterior a la fecha y hora de inicio';
                        isValid = false;
                    }
                }

                return isValid;
            }

            // Función para mostrar errores del servidor
            function mostrarErrorServidor(mensaje) {
                const errorContainer = document.getElementById('errorContainer');
                errorContainer.classList.remove('d-none');
                errorContainer.innerHTML = mensaje;
            }

            // Función para limpiar el formulario
            function limpiarFormulario() {
                document.getElementById('formCrearEvento').reset();
                document.getElementById('errorContainer').classList.add('d-none');
                document.getElementById('errorContainer').innerHTML = '';
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            }

            // Eventos para los campos de fecha y hora
            document.getElementById('nuevaFechaInicio').addEventListener('change', function() {
                const fecha = this.value;
                const horaSelect = document.getElementById('nuevaHoraInicio');
                // Si no hay hora seleccionada, asignar la hora por defecto
                if (!horaSelect.value) {
                    horaSelect.value = '12:00';
                }
                document.getElementById('nuevaFechaInicioCompleta').value = combinarFechaHora(fecha, horaSelect.value);
            });

            document.getElementById('nuevaHoraInicio').addEventListener('change', function() {
                const fecha = document.getElementById('nuevaFechaInicio').value;
                const hora = this.value;
                document.getElementById('nuevaFechaInicioCompleta').value = combinarFechaHora(fecha, hora);
            });

            document.getElementById('nuevaFechaFin').addEventListener('change', function() {
                const fecha = this.value;
                const horaSelect = document.getElementById('nuevaHoraFin');
                // Si no hay hora seleccionada, asignar la hora por defecto
                if (!horaSelect.value) {
                    horaSelect.value = '12:30';
                }
                document.getElementById('nuevaFechaFinCompleta').value = combinarFechaHora(fecha, horaSelect.value);
            });

            document.getElementById('nuevaHoraFin').addEventListener('change', function() {
                const fecha = document.getElementById('nuevaFechaFin').value;
                const hora = this.value;
                document.getElementById('nuevaFechaFinCompleta').value = combinarFechaHora(fecha, hora);
            });

            // al hacer click en boton flotante, abre modal de crear eventos
            document.getElementById('fabCrearEvento').onclick = function() {
                // Asignar valores por defecto
                document.getElementById('nuevaHoraInicio').value = '12:00';
                document.getElementById('nuevaHoraFin').value = '12:30';
                // También puedes asignar la fecha de hoy si quieres
                let hoy = new Date().toISOString().split('T')[0];
                document.getElementById('nuevaFechaInicio').value = hoy;
                document.getElementById('nuevaFechaFin').value = hoy;
                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('crearEventoModal'));
                modal.show();
            };

            // Inicializar el calendario
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                console.error('No se encontró el elemento del calendario');
                return;
            }

            // Variables para la vista de agenda
            const agendaViewEl = document.getElementById('agendaView');
            const agendaListEl = document.getElementById('agendaList');
            let isAgendaView = false;

            // Función para cambiar entre vista de calendario y agenda
            function toggleAgendaView() {
                isAgendaView = !isAgendaView;
                if (isAgendaView) {
                    calendarEl.classList.add('d-none');
                    agendaViewEl.classList.remove('d-none');
                    document.getElementById('btnAgendaView').innerHTML = '<i class="ri-calendar-line"></i> Ver calendario';
                    loadAgendaView();
                } else {
                    calendarEl.classList.remove('d-none');
                    agendaViewEl.classList.add('d-none');
                    document.getElementById('btnAgendaView').innerHTML = '<i class="ri-list-check-line"></i> Ver agenda';
                }
            }

            // Función para actualizar la vista de agenda si está activa
            function updateAgendaViewIfActive() {
                if (isAgendaView) {
                    loadAgendaView();
                }
            }

            // Evento para el botón de agenda
            document.getElementById('btnAgendaView').addEventListener('click', toggleAgendaView);

            // Función para cargar los eventos en la vista de agenda
            function loadAgendaView() {
                // Obtener todos los eventos del calendario
                const events = calendar.getEvents();

                // Ordenar eventos por fecha de inicio
                events.sort((a, b) => a.start - b.start);

                // Limpiar la lista de agenda
                agendaListEl.innerHTML = '';

                // Agrupar eventos por fecha
                const eventsByDate = {};
                events.forEach(event => {
                    const dateStr = event.start.toISOString().split('T')[0];
                    if (!eventsByDate[dateStr]) {
                        eventsByDate[dateStr] = [];
                    }
                    eventsByDate[dateStr].push(event);
                });

                // Si no hay eventos, mostrar mensaje
                if (Object.keys(eventsByDate).length === 0) {
                    agendaListEl.innerHTML = '<div class="alert alert-info">No hay eventos para mostrar</div>';
                    return;
                }

                // Crear elementos para cada fecha y sus eventos
                for (const dateStr in eventsByDate) {
                    const date = new Date(dateStr);
                    const formattedDate = date.toLocaleDateString('es-ES', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    // Crear encabezado de fecha desplegable
                    const dateHeader = document.createElement('div');
                    dateHeader.className = 'list-group-item list-group-item-secondary';
                    dateHeader.style.cursor = 'pointer';
                    dateHeader.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">${formattedDate}</h5>
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    `;
                    agendaListEl.appendChild(dateHeader);

                    // Crear contenedor para los eventos de esta fecha (inicialmente oculto)
                    const eventsContainer = document.createElement('div');
                    eventsContainer.className = 'events-container d-none';
                    eventsContainer.dataset.date = dateStr;
                    agendaListEl.appendChild(eventsContainer);

                    // Añadir evento click al encabezado para mostrar/ocultar eventos
                    dateHeader.addEventListener('click', function() {
                        const icon = this.querySelector('i');
                        if (eventsContainer.classList.contains('d-none')) {
                            eventsContainer.classList.remove('d-none');
                            icon.classList.remove('ri-arrow-down-s-line');
                            icon.classList.add('ri-arrow-up-s-line');
                        } else {
                            eventsContainer.classList.add('d-none');
                            icon.classList.remove('ri-arrow-up-s-line');
                            icon.classList.add('ri-arrow-down-s-line');
                        }
                    });

                    // Crear elementos para cada evento en esta fecha
                    eventsByDate[dateStr].forEach(event => {
                        const eventItem = document.createElement('div');
                        eventItem.className = 'list-group-item';
                        eventItem.style.borderLeft = `5px solid ${event.backgroundColor || event.borderColor || '#3788d8'}`;

                        // Formatear hora
                        let timeStr = '';
                        if (event.allDay) {
                            timeStr = 'Todo el día';
                        } else {
                            const startTime = event.start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                            const endTime = event.end ? event.end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) : '';
                            timeStr = startTime + (endTime ? ` - ${endTime}` : '');
                        }

                        // Crear contenido del evento
                        eventItem.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1">${event.title}</h6>
                                <span class="badge bg-secondary">${timeStr}</span>
                            </div>
                            <p class="mb-1 text-muted small">${event.extendedProps?.tipo_evento_nombre || ''}</p>
                            ${event.extendedProps?.descripcion ? `<p class="mb-1">${event.extendedProps.descripcion}</p>` : ''}
                            ${event.extendedProps?.ubicacion ? `<p class="mb-0 small"><i class="ri-map-pin-line"></i> ${event.extendedProps.ubicacion}</p>` : ''}
                        `;

                        // Añadir estilo de cursor para indicar que es clickeable
                        eventItem.style.cursor = 'pointer';

                        // Añadir evento para mostrar detalles al hacer clic
                        eventItem.addEventListener('click', function() {
                            // Usar la función mostrarModalEvento para mostrar los detalles del evento
                            mostrarModalEvento(event);

                            // La función mostrarModalEvento se encarga de llenar el modal con los datos del evento

                            // La función mostrarModalEvento ya se encarga de mostrar todos los detalles del evento,
                            // configurar la visibilidad de los campos, formatear las fechas y configurar los permisos
                            // para los botones de edición y eliminación.
                            // La función mostrarModalEvento ya se encarga de mostrar el modal
                        });

                        eventsContainer.appendChild(eventItem);
                    });
                }
            }

            // Evento para el botón de agenda
            document.getElementById('btnAgendaView').addEventListener('click', toggleAgendaView);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    prev: '<',
                    next: '>',
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                views: {
                    dayGridMonth: {
                        titleFormat: { year: 'numeric', month: 'long' }
                    },
                    timeGridWeek: {
                        titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
                    },
                    timeGridDay: {
                        titleFormat: { year: 'numeric', month: 'long', day: '2-digit', weekday: 'long' }
                    }
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                slotMinTime: '00:00:00',
                slotMaxTime: '24:00:00',
                slotDuration: '01:00:00',
                slotLabelInterval: '01:00:00',
                allDayText: 'Todo el día',
                noEventsText: 'No hay eventos para mostrar',
                moreLinkText: 'más',
                events: @json($eventos ?? []),
                editable: false,
                selectable: true,
                dayMaxEventRows: 3,
                moreLinkClick: 'popover',
                eventDidMount: function(info) {
                    const props = info.event.extendedProps;
                    const esRecordatorioPersonal = props.tipo_evento_nombre === 'Recordatorio Personal';
                    const esCreador = props.creado_por == userId;

                    if (userRole === 'Administrador' || userRole === 'Profesor') {
                        info.event.setProp('editable', true);
                    } else if (userRole === 'Alumno') {
                        if (esRecordatorioPersonal && esCreador) {
                            info.event.setProp('editable', true);
                        } else {
                            info.event.setProp('editable', false);
                        }
                    }
                },
                dateClick: function(info) {
                    if (calendar.view.type === 'timeGridDay') {
                        // Preseleccionar fecha y hora en el formulario
                        const startDate = new Date(info.date);
                        const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
                        const startDateStr = startDate.toISOString().split('T')[0];
                        const endDateStr = endDate.toISOString().split('T')[0];
                        const startHour = startDate.getHours().toString().padStart(2, '0');
                        const startMinute = Math.floor(startDate.getMinutes() / 15) * 15;
                        const endHour = endDate.getHours().toString().padStart(2, '0');
                        const endMinute = Math.floor(endDate.getMinutes() / 15) * 15;
                        const startTimeStr = `${startHour}:${startMinute.toString().padStart(2, '0')}`;
                        const endTimeStr = `${endHour}:${endMinute.toString().padStart(2, '0')}`;
                        document.getElementById('nuevaFechaInicio').value = startDateStr;
                        document.getElementById('nuevaFechaFin').value = endDateStr;
                        document.getElementById('nuevaHoraInicio').value = startTimeStr;
                        document.getElementById('nuevaHoraFin').value = endTimeStr;
                        document.getElementById('nuevaFechaInicioCompleta').value = `${startDateStr}T${startTimeStr}`;
                        document.getElementById('nuevaFechaFinCompleta').value = `${endDateStr}T${endTimeStr}`;
                        const modal = new bootstrap.Modal(document.getElementById('crearEventoModal'));
                        modal.show();
                    } else {
                        calendar.changeView('timeGridDay', info.date);
                    }
                },
                eventClick: function(info) {
                    const calendar = info.view.calendar;
                    const event = info.event;
                    const start = event.start;

                    if (calendar.view.type === 'timeGridDay') {
                        mostrarModalEvento(event);
                    } else {
                        calendar.changeView('timeGridDay', start);
                    }
                },
                eventDrop: function(info) {
                    const props = info.event.extendedProps;
                    const esRecordatorioPersonal = props.tipo_evento_nombre === 'Recordatorio Personal';
                    const esCreador = props.creado_por == userId;

                    let url = `/eventos/${info.event.id}`;
                    if (userRole === 'Alumno') {
                        if (esRecordatorioPersonal && esCreador) {
                            url = `/events/reminders/${info.event.id}`;
                        }
                    }

                    fetch(url, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            fecha_inicio: info.event.start.toISOString().slice(0, 19).replace('T', ' '),
                            fecha_fin: info.event.end
                                ? info.event.end.toISOString().slice(0, 19).replace('T', ' ')
                                : info.event.start.toISOString().slice(0, 19).replace('T', ' ')
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al actualizar la fecha del evento.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error al actualizar la fecha del evento');
                        }
                        // Actualizar la vista de agenda si está activa
                        updateAgendaViewIfActive();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar la fecha del evento.');
                        info.revert();
                    });
                },
                dateClick: function(info) {
                    if (calendar.view.type === 'timeGridDay') {
                        // Preseleccionar fecha y hora en el formulario
                        const startDate = new Date(info.date);
                        const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
                        const startDateStr = startDate.toISOString().split('T')[0];
                        const endDateStr = endDate.toISOString().split('T')[0];
                        const startHour = startDate.getHours().toString().padStart(2, '0');
                        const startMinute = Math.floor(startDate.getMinutes() / 15) * 15;
                        const endHour = endDate.getHours().toString().padStart(2, '0');
                        const endMinute = Math.floor(endDate.getMinutes() / 15) * 15;
                        const startTimeStr = `${startHour}:${startMinute.toString().padStart(2, '0')}`;
                        const endTimeStr = `${endHour}:${endMinute.toString().padStart(2, '0')}`;
                        document.getElementById('nuevaFechaInicio').value = startDateStr;
                        document.getElementById('nuevaFechaFin').value = endDateStr;
                        document.getElementById('nuevaHoraInicio').value = startTimeStr;
                        document.getElementById('nuevaHoraFin').value = endTimeStr;
                        document.getElementById('nuevaFechaInicioCompleta').value = `${startDateStr}T${startTimeStr}`;
                        document.getElementById('nuevaFechaFinCompleta').value = `${endDateStr}T${endTimeStr}`;
                        const modal = new bootstrap.Modal(document.getElementById('crearEventoModal'));
                        modal.show();
                    } else {
                        calendar.changeView('timeGridDay', info.date);
                    }
                }
            });

            // Renderizar el calendario
            calendar.render();

            // Configurar los botones después de que el calendario esté inicializado
            document.getElementById('btnEliminar').onclick = function() {
                const btnEliminar = document.getElementById('btnEliminar');
                const tipoEventoId = document.getElementById('eventoTipoId').value;
                const creadoPor = document.getElementById('eventoCreadoPor').value;
                const esRecordatorioPersonal = document.getElementById('tipoEventoNombre').textContent === 'Recordatorio Personal';
                const esCreador = creadoPor == userId;

                if (!(userRole === 'Administrador' || userRole === 'Profesor')) {
                    if (!(esRecordatorioPersonal && esCreador)) {
                        alert('No tienes permiso para eliminar este evento.');
                        return;
                    }
                }

                let id = document.getElementById('eventoId').value;
                if (confirm('¿Seguro que deseas eliminar este evento?')) {
                    btnEliminar.disabled = true;
                    btnEliminar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...';

                    let deleteUrl = `/eventos/${id}`;
                    if (userRole === 'Alumno') {
                        if (esRecordatorioPersonal && esCreador) {
                            deleteUrl = `/events/reminders/${id}`;
                        }
                    }

                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        btnEliminar.disabled = false;
                        btnEliminar.innerHTML = 'Eliminar';

                        if (response.ok) {
                            var modal = bootstrap.Modal.getInstance(document.getElementById('eventoModal'));
                            modal.hide();
                            calendar.getEventById(id).remove();
                            calendar.refetchEvents();
                            updateAgendaViewIfActive();
                            alert('Evento eliminado exitosamente.');
                        } else {
                            response.json().then(data => {
                                alert(data.message || 'Error al eliminar el evento.');
                            });
                        }
                    })
                    .catch(error => {
                        btnEliminar.disabled = false;
                        btnEliminar.innerHTML = 'Eliminar';
                        console.error('Error:', error);
                        alert('Error al eliminar el evento.');
                    });
                }
            };

            document.getElementById('btnGuardar').onclick = function() {
                const btnGuardar = document.getElementById('btnGuardar');
                const tipoEventoId = document.getElementById('eventoTipoId').value;
                const creadoPor = document.getElementById('eventoCreadoPor').value;
                const esRecordatorioPersonal = document.getElementById('tipoEventoNombre').textContent === 'Recordatorio Personal';
                const esCreador = creadoPor == userId;

                if (!(userRole === 'Administrador' || userRole === 'Profesor')) {
                    if (!(esRecordatorioPersonal && esCreador)) {
                        alert('No tienes permiso para editar este evento.');
                        return;
                    }
                }

                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

                let id = document.getElementById('eventoId').value;
                let titulo = document.getElementById('titulo').value;
                let descripcion = document.getElementById('descripcion').value;

                if (!titulo) {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = 'Guardar cambios';
                    alert('El título no puede estar vacío.');
                    return;
                }

                let updateUrl = `/eventos/${id}`;
                if (userRole === 'Alumno') {
                    if (esRecordatorioPersonal && esCreador) {
                        updateUrl = `/events/reminders/${id}`;
                    }
                }

                fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        titulo: titulo,
                        descripcion: descripcion
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = 'Guardar cambios';

                    if (data.success) {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('eventoModal'));
                        modal.hide();
                        let event = calendar.getEventById(id);
                        event.setProp('title', titulo);
                        event.setExtendedProp('descripcion', descripcion);
                        updateAgendaViewIfActive();
                        alert('Evento actualizado exitosamente.');
                    } else {
                        alert('Error al actualizar el evento.');
                    }
                })
                .catch(error => {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = 'Guardar cambios';
                    console.error('Error:', error);
                    alert('Error al actualizar el evento.');
                });
            };

            // Evento para el botón de crear evento
            document.getElementById('btnCrearEvento').onclick = function() {
                if (!validarFormulario()) {
                    return;
                }

                const btnCrearEvento = document.getElementById('btnCrearEvento');
                const spinner = btnCrearEvento.querySelector('.spinner-border');
                btnCrearEvento.disabled = true;
                spinner.classList.remove('d-none');

                const titulo = document.getElementById('nuevoTitulo').value;
                const descripcion = document.getElementById('nuevaDescripcion').value;
                const ubicacion = document.getElementById('nuevaUbicacion').value;
                const url_virtual = document.getElementById('nuevaUrlVirtual').value;
                const tipo_evento_id = document.getElementById('nuevoTipoEvento').value;
                const fecha_inicio = document.getElementById('nuevaFechaInicioCompleta').value;
                const fecha_fin = document.getElementById('nuevaFechaFinCompleta').value;

                fetch('/eventos', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        titulo: titulo,
                        descripcion: descripcion,
                        ubicacion: ubicacion,
                        url_virtual: url_virtual,
                        tipo_evento_id: tipo_evento_id,
                        fecha_inicio: fecha_inicio,
                        fecha_fin: fecha_fin
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Error al crear el evento');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('crearEventoModal'));
                        modal.hide();

                        let tipoEventoSelect = document.getElementById('nuevoTipoEvento');
                        let color;

                        if (tipoEventoSelect.tagName === 'SELECT') {
                            let selectedOption = tipoEventoSelect.options[tipoEventoSelect.selectedIndex];
                            color = selectedOption.getAttribute('data-color');
                        } else {
                            color = tipoEventoSelect.getAttribute('data-color');
                        }

                        calendar.addEvent({
                            id: data.evento.id,
                            title: data.evento.titulo,
                            start: data.evento.fecha_inicio,
                            end: data.evento.fecha_fin,
                            color: color,
                            extendedProps: {
                                descripcion: data.evento.descripcion
                            }
                        });

                        limpiarFormulario();
                        updateAgendaViewIfActive();
                        alert('Evento creado exitosamente.');
                    }
                })
                .catch(error => {
                    mostrarErrorServidor(error.message);
                })
                .finally(() => {
                    btnCrearEvento.disabled = false;
                    spinner.classList.add('d-none');
                });
            };

            // Limpiar formulario cuando se cierra el modal
            document.getElementById('crearEventoModal').addEventListener('hidden.bs.modal', function () {
                limpiarFormulario();
            });

            // Habilitar edición solo tras click en el lápiz
            document.getElementById('btnEditarEvento').onclick = function() {
                document.getElementById('titulo').disabled = false;
                document.getElementById('descripcion').disabled = false;
                document.getElementById('btnGuardar').style.display = '';
                document.getElementById('titulo').focus();
            };

            // Función para mostrar el modal de detalles del evento
            function mostrarModalEvento(event) {
                const props = event.extendedProps;
                document.getElementById('eventoId').value = event.id;
                document.getElementById('eventoTipoId').value = props.tipo_evento_id || '';
                document.getElementById('eventoCreadoPor').value = props.creado_por || '';
                document.getElementById('titulo').value = event.title;
                document.getElementById('titulo').disabled = true;
                // Descripción
                if (props.descripcion) {
                    document.getElementById('grupoDescripcion').style.display = '';
                    document.getElementById('descripcion').value = props.descripcion;
                } else {
                    document.getElementById('grupoDescripcion').style.display = 'none';
                }
                document.getElementById('descripcion').disabled = true;
                // Tipo de evento
                if (props.tipo_evento_nombre) {
                    document.getElementById('grupoTipoEvento').style.display = '';
                    document.getElementById('tipoEventoNombre').textContent = props.tipo_evento_nombre;
                } else {
                    document.getElementById('grupoTipoEvento').style.display = 'none';
                }
                // Creado por
                if (props.creado_por_nombre) {
                    document.getElementById('grupoCreadoPor').style.display = '';
                    document.getElementById('creadoPorNombre').textContent = props.creado_por_nombre;
                } else {
                    document.getElementById('grupoCreadoPor').style.display = 'none';
                }
                // Ubicación
                if (props.ubicacion) {
                    document.getElementById('grupoUbicacion').style.display = '';
                    document.getElementById('ubicacion').textContent = props.ubicacion;
                } else {
                    document.getElementById('grupoUbicacion').style.display = 'none';
                }
                // URL Virtual
                if (props.url_virtual) {
                    document.getElementById('grupoUrlVirtual').style.display = '';
                    document.getElementById('urlVirtual').textContent = props.url_virtual;
                } else {
                    document.getElementById('grupoUrlVirtual').style.display = 'none';
                }
                // Fechas
                if (props.created_at) {
                    document.getElementById('grupoFechaCreacion').style.display = '';
                    const fechaCreacion = new Date(props.created_at);
                    document.getElementById('fechaCreacion').textContent = formatearFecha(fechaCreacion);
                } else {
                    document.getElementById('grupoFechaCreacion').style.display = 'none';
                }
                if (event.start) {
                    document.getElementById('grupoFechaInicio').style.display = '';
                    const fechaInicio = new Date(event.start);
                    document.getElementById('fechaInicio').textContent = formatearFecha(fechaInicio);
                } else {
                    document.getElementById('grupoFechaInicio').style.display = 'none';
                }
                if (event.end) {
                    document.getElementById('grupoFechaFin').style.display = '';
                    const fechaFin = new Date(event.end);
                    document.getElementById('fechaFin').textContent = formatearFecha(fechaFin);
                } else {
                    document.getElementById('grupoFechaFin').style.display = 'none';
                }
                // Control de botones editar/borrar para alumnos
                const btnEditar = document.getElementById('btnEditarEvento');
                const btnEliminar = document.getElementById('btnEliminar');
                let mostrarBotones = true;
                if (userRole === 'Alumno') {
                    // Solo puede editar/borrar si es recordatorio personal y es el creador
                    const esRecordatorioPersonal = props.tipo_evento_nombre === 'Recordatorio Personal';
                    const esCreador = props.creado_por == userId;
                    mostrarBotones = esRecordatorioPersonal && esCreador;
                }
                btnEditar.style.display = mostrarBotones ? '' : 'none';
                btnEliminar.style.display = mostrarBotones ? '' : 'none';
                // Deshabilitar edición
                document.getElementById('titulo').disabled = true;
                document.getElementById('descripcion').disabled = true;
                document.getElementById('btnGuardar').style.display = 'none';
                const modal = new bootstrap.Modal(document.getElementById('eventoModal'));
                modal.show();
            }
        });
    </script>
</div>
@endsection

