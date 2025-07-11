@extends('template.base-alumno')

@section('title', 'Mi Perfil')
@section('title-page', 'Mi Perfil')

@section('content')
<div class="col-12 col-lg-8 mx-auto">
    <div class="card card-hover mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="ri-user-line me-2"></i>Datos de mi perfil</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr><th>Nombre</th><td>{{ $persona->nombre }}</td></tr>
                    <tr><th>Primer Apellido</th><td>{{ $persona->apellido1 }}</td></tr>
                    <tr><th>Segundo Apellido</th><td>{{ $persona->apellido2 }}</td></tr>
                    <tr><th>DNI</th><td>{{ $persona->dni }}</td></tr>
                    <tr><th>Teléfono</th><td>{{ $persona->tfno }}</td></tr>
                    <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                    @if($persona->direccion)
                        <tr><th>Calle</th><td>{{ $persona->direccion->calle }}</td></tr>
                        <tr><th>Número</th><td>{{ $persona->direccion->numero }}</td></tr>
                        <tr><th>Piso</th><td>{{ $persona->direccion->piso }}</td></tr>
                        <tr><th>Código Postal</th><td>{{ $persona->direccion->cp }}</td></tr>
                        <tr><th>Ciudad</th><td>{{ $persona->direccion->ciudad }}</td></tr>
                        <tr><th>Provincia</th><td>{{ $persona->direccion->provincia }}</td></tr>
                        <tr><th>País</th><td>{{ $persona->direccion->pais }}</td></tr>
                    @endif
                    <tr>
                        <th>Foto de perfil</th>
                        <td>
                            <img src="{{ $persona->foto_perfil ? asset('storage/' . $persona->foto_perfil) : asset('/admin/img/avatars/avatar2.jpg') }}" alt="Foto de perfil" class="rounded-circle" width="80" height="80">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary">
                    <i class="ri-user-settings-line me-1"></i> Editar Perfil
                </a>
                <a href="{{ route('alumno.home') }}" class="btn btn-outline-primary">
                    <i class="ri-arrow-go-back-line me-1"></i> Volver al inicio
                </a>
            </div>
        </div>
    </div>
    @include('profile._pagos')
</div>
@endsection
