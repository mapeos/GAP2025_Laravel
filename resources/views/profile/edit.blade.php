@extends('template.base-alumno')

@section('title', 'Editar Perfil')
@section('title-page', 'Editar Perfil')

@section('content')
<div class="col-12 col-lg-8 mx-auto">
    <div class="card card-hover mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="ri-user-settings-line me-2"></i>Editar mi perfil</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $persona->nombre) }}" required autofocus class="form-control @error('nombre') is-invalid @enderror" />
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="apellido1" class="form-label">Primer Apellido</label>
                        <input id="apellido1" type="text" name="apellido1" value="{{ old('apellido1', $persona->apellido1) }}" required class="form-control @error('apellido1') is-invalid @enderror" />
                        @error('apellido1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="apellido2" class="form-label">Segundo Apellido</label>
                        <input id="apellido2" type="text" name="apellido2" value="{{ old('apellido2', $persona->apellido2) }}" class="form-control @error('apellido2') is-invalid @enderror" />
                        @error('apellido2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="dni" class="form-label">DNI</label>
                        <input id="dni" type="text" name="dni" value="{{ old('dni', $persona->dni) }}" required class="form-control @error('dni') is-invalid @enderror" />
                        @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tfno" class="form-label">Teléfono</label>
                        <input id="tfno" type="text" name="tfno" value="{{ old('tfno', $persona->tfno) }}" class="form-control @error('tfno') is-invalid @enderror" />
                        @error('tfno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <hr class="my-4">
                <h6 class="mb-3">Dirección</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="calle" class="form-label">Calle</label>
                        <input id="calle" type="text" name="calle" value="{{ old('calle', $persona->direccion->calle ?? '') }}" required class="form-control @error('calle') is-invalid @enderror" />
                        @error('calle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label for="numero" class="form-label">Número</label>
                        <input id="numero" type="text" name="numero" value="{{ old('numero', $persona->direccion->numero ?? '') }}" class="form-control @error('numero') is-invalid @enderror" />
                        @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label for="piso" class="form-label">Piso</label>
                        <input id="piso" type="text" name="piso" value="{{ old('piso', $persona->direccion->piso ?? '') }}" class="form-control @error('piso') is-invalid @enderror" />
                        @error('piso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="cp" class="form-label">Código Postal</label>
                        <input id="cp" type="text" name="cp" value="{{ old('cp', $persona->direccion->cp ?? '') }}" required class="form-control @error('cp') is-invalid @enderror" />
                        @error('cp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="ciudad" class="form-label">Ciudad</label>
                        <input id="ciudad" type="text" name="ciudad" value="{{ old('ciudad', $persona->direccion->ciudad ?? '') }}" required class="form-control @error('ciudad') is-invalid @enderror" />
                        @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="provincia" class="form-label">Provincia</label>
                        <input id="provincia" type="text" name="provincia" value="{{ old('provincia', $persona->direccion->provincia ?? '') }}" required class="form-control @error('provincia') is-invalid @enderror" />
                        @error('provincia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="pais" class="form-label">País</label>
                        <input id="pais" type="text" name="pais" value="{{ old('pais', $persona->direccion->pais ?? '') }}" required class="form-control @error('pais') is-invalid @enderror" />
                        @error('pais')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i> Guardar
                    </button>
                    <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                        <i class="ri-arrow-go-back-line me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
