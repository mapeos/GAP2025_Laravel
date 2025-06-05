<<<<<<< HEAD
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista alumnos</title>
</head>
<body style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh;">
    <h1 style="font-size: 4rem; font-weight: bold;">Vista alumnos</h1>
</body>
</html>
=======
@extends('template.base-alumno')

@section('title', 'Alumno')
@section('title-page', 'Home')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="alert alert-welcome p-4 rounded-3">
            <h4 class="alert-heading mb-3">Bienvenido, {{ Auth::user()->name }}</h4>
            <p class="mb-0">Este es tu panel de control personal. Aquí podrás gestionar tu perfil y acceder a todas tus funciones.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-hover h-100 border-primary">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="ri-book-open-line me-2"></i>
                <h5 class="card-title mb-0">Mis cursos</h5>
            </div>
            <div class="card-body text-center text-muted">
                <i class="ri-graduation-cap-line display-4 mb-3"></i>
                <p class="mb-0">Aquí verás los cursos en los que estás inscrito.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover h-100 border-success">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="ri-add-circle-line me-2"></i>
                <h5 class="card-title mb-0">Cursos disponibles</h5>
            </div>
            <div class="card-body text-center">
                @php
                    $cursosCount = \App\Models\Curso::where('estado', 'abierto')->count();
                @endphp
                <i class="ri-booklet-line display-4 mb-3 text-success"></i>
                <h2 class="fw-bold mb-2">{{ $cursosCount }}</h2>
                <p class="mb-0">Curso{{ $cursosCount == 1 ? '' : 's' }} disponible{{ $cursosCount == 1 ? '' : 's' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover h-100 border-info">
            <div class="card-header bg-info text-white d-flex align-items-center">
                <i class="ri-calendar-event-line me-2"></i>
                <h5 class="card-title mb-0">Calendario</h5>
            </div>
            <div class="card-body text-center text-muted">
                <i class="ri-calendar-2-line display-4 mb-3"></i>
                <p class="mb-0">Aquí se mostrará tu calendario de actividades y eventos.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card card-hover h-100">
            <div class="card-header border-0 bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="ri-user-line me-2"></i>
                    <h5 class="card-title mb-0">Mi Perfil</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Nombre:</strong> {{ Auth::user()->name }}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> {{ Auth::user()->email }}
                </div>
                @if(Auth::user()->persona)
                    <div class="mb-3">
                        <strong>DNI:</strong> {{ Auth::user()->persona->dni }}
                    </div>
                    <div class="mb-3">
                        <strong>Teléfono:</strong> {{ Auth::user()->persona->tfno ?? 'No especificado' }}
                    </div>
                @else
                    <div class="alert alert-info">
                        Completa tu información personal para acceder a todas las funcionalidades.
                    </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('profile.show') }}" class="btn btn-primary btn-sm">
                        <i class="ri-user-line me-1"></i> Ver Perfil Completo
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-sm">
                        <i class="ri-user-settings-line me-1"></i> Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
>>>>>>> 2863447 (Ajustes en AuthController Api)
