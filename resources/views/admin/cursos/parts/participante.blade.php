<div class="card h-100">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span>Participante</span>
        <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
           class="btn btn-warning btn-sm fw-bold shadow"
           title="Añadir participante">
            <i class="fa fa-user-plus me-1"></i> Añadir participante
        </a>
    </div>
    <div class="card-body">
        <p>Participantes inscritos: {{ $curso->personas->count() ?? 0 }}</p>
        @if($curso->personas && $curso->personas->count())
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($curso->personas as $index => $persona)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $persona->nombre }}</td>
                                <td>{{ $persona->apellido1 }} {{ $persona->apellido2 }}</td>
                                <td>{{ $persona->user->email ?? '-' }}</td>
                                <td>
                                    {{ $persona->pivot->rol_participacion_id ?? '-' }}
                                    {{-- Si tienes relación con el modelo de rol, puedes mostrar el nombre del rol aquí --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mt-3">No hay participantes inscritos en este curso.</div>
        @endif
    </div>
</div>