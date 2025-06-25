@extends('template.base')
@section('title', 'Página de Prueba')
@section('title-sidebar', auth()->user()?->name ?? 'Dashboard Admin')
@section('title-page', 'Página de Prueba')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Página de Prueba</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Página de Prueba del Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Información del Sistema</h6>
                        <ul>
                            <li><strong>Usuario:</strong> {{ auth()->user()->name }}</li>
                            <li><strong>Email:</strong> {{ auth()->user()->email }}</li>
                            <li><strong>Rol:</strong> {{ auth()->user()->roles->first()->name ?? 'Sin rol' }}</li>
                            <li><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
                        </ul>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>Estadísticas Rápidas</h6>
                                    <p>Usuarios totales: {{ \App\Models\User::count() }}</p>
                                    <p>Cursos totales: {{ \App\Models\Curso::count() }}</p>
                                    <p>Noticias totales: {{ \App\Models\News::count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>Estado del Sistema</h6>
                                    <p>✅ Base de datos: Conectada</p>
                                    <p>✅ Cache: Funcionando</p>
                                    <p>✅ Sesión: Activa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 