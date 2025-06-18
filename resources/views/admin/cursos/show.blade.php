@extends('template.base')

@inject('storage', 'Illuminate\Support\Facades\Storage')

@section('title', 'Detalles del Curso')

@section('content')
<h1>Detalles del Curso: {{ $curso->titulo }}</h1>

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if ($curso->portada_path)
    <div>
        <img src="{{ asset('storage/' . $curso->portada_path) }}" alt="Portada del curso" style="max-width: 300px;">
    </div>
@endif

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <td>{{ $curso->id }}</td>
    </tr>
    <tr>
        <th>Título</th>
        <td>{{ $curso->titulo }}</td>
    </tr>
    <tr>
        <th>Descripción</th>
        <td>{{ $curso->descripcion }}</td>
    </tr>
    <tr>
        <th>Fecha Inicio</th>
        <td>{{ $curso->fechaInicio }}</td>
    </tr>
    <tr>
        <th>Fecha Fin</th>
        <td>{{ $curso->fechaFin }}</td>
    </tr>
    <tr>
        <th>Plazas</th>
        <td>{{ $curso->plazas }}</td>
    </tr>
    <tr>
        <th>Estado</th>
        <td>{{ $curso->estado }}</td>
    </tr>
</table>

{{-- Mostrar el temario si existe --}}

@if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
<h2>Temario Subido</h2>
<a href="{{ asset('storage/' . $curso->temario_path) }}" target="_blank" class="btn btn-info">
    📄 Ver/Descargar Temario
</a>
@else
<p>No se ha subido ningún temario.</p>
@endif

<h2>Subir Temario</h2>
<form action="{{ route('admin.cursos.upload', $curso->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="temario">Subir temario:</label>
        <input type="file" name="temario" accept=".pdf,.doc,.docx" required>
    </div>
    <button type="submit">Subir temario</button>
</form>

<a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
@endsection