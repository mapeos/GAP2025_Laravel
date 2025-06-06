extends('template.base')

@section('title', 'Creación de Participantes')

@section('content')
    <h1>Crear Participante</h1>

    <form action="{{ route('admin.participantes.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ingrese el nombre" required>
        </div>

        <div class="form-group">
            <label for="apellido1">Primer Apellido</label>
            <input type="text" name="apellido1" id="apellido1" class="form-control" placeholder="Ingrese el primer apellido" required>
        </div>

        <div class="form-group">
            <label for="apellido2">Segundo Apellido</label>
            <input type="text" name="apellido2" id="apellido2" class="form-control" placeholder="Ingrese el segundo apellido">
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Ingrese el correo electrónico" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Ingrese el teléfono">
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
@endsection