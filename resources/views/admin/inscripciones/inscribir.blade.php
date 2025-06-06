@extends('template.base-admin')

@section('title', 'Inscribir Personas')

@section('content')
<h1>Inscribir Personas en {{ $curso->titulo }}</h1>
<h2>Personas Disponibles</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($personas as $persona)
        <tr>
            <td>{{ $persona->id }}</td>
            <td>{{ $persona->nombre }} {{ $persona->apellido1 }} {{ $persona->apellido2 }}</td>
            <td>
                <form action="{{ route('admin.inscripciones.cursos.inscribir', $curso->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="persona_id" value="{{ $persona->id }}">
                    <div class="form-group">
                        <label for="rol_participacion_id">Rol de Participación</label>
                        <select name="rol_participacion_id" id="rol_participacion_id" class="form-control" required>
                            @foreach($rolesParticipacion as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success">Agregar</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">No hay personas disponibles.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<h2>Personas Inscritas</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
        </tr>
    </thead>
    <tbody>
        @forelse($inscritos as $inscrito)
        <tr>
            <td>{{ $inscrito->id }}</td>
            <td>{{ $inscrito->nombre }} {{ $inscrito->apellido1 }} {{ $inscrito->apellido2 }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="2" class="text-center">No hay personas inscritas.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection