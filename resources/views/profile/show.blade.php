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
            <dl class="row mb-0">
                <dt class="col-sm-4">Nombre</dt>
                <dd class="col-sm-8">{{ $persona->nombre }}</dd>
                <dt class="col-sm-4">Primer Apellido</dt>
                <dd class="col-sm-8">{{ $persona->apellido1 }}</dd>
                <dt class="col-sm-4">Segundo Apellido</dt>
                <dd class="col-sm-8">{{ $persona->apellido2 }}</dd>
                <dt class="col-sm-4">DNI</dt>
                <dd class="col-sm-8">{{ $persona->dni }}</dd>
                <dt class="col-sm-4">Teléfono</dt>
                <dd class="col-sm-8">{{ $persona->tfno }}</dd>
                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8">{{ $user->email }}</dd>
                @if($persona->direccion)
                    <dt class="col-sm-4">Calle</dt>
                    <dd class="col-sm-8">{{ $persona->direccion->calle }}</dd>
                    <dt class="col-sm-4">Número</dt>
                    <dd class="col-sm-8">{{ $persona->direccion->numero }}</dd>
                    <dt class="col-sm-4">Piso</dt>
                    <dd class="col-sm-8">{{ $persona->direccion->piso }}</dd>
                    <dt class="col-sm-4">Código Postal</dt>
                    <dd class="col-sm-8">{{ $persona->direccion->cp }}</dd>
                    <dt class="col-sm-4">Ciudad</dt>
                    <dd class="col-sm-8">{{ $persona->direccion->ciudad }}</dd>
                    <dt class="col-sm-4">Provincia</dt>
                    <dd class="col-sm-8">{{ $persona->direccion->provincia }}</dd>
                    <dt class="col-sm-4">País</dt>
                    <dd class="col-sm-8">{{ $persona->direccion->pais }}</dd>
                @endif
            </dl>
            <div class="mt-4">
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary">
                    <i class="ri-user-settings-line me-1"></i> Editar Perfil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
