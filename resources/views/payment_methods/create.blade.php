@extends('template.base-admin')

@section('title', 'Crear Método de Pago')
@section('title-sidebar', 'Gestión de Pagos')
@section('title-page', 'Nuevo Método de Pago')

@section('content')
    <h1>Crear Nuevo Método de Pago</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payment-methods.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Nombre:</label><br>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>

        <div>
            <label for="description">Descripción:</label><br>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
        </div>

        <div>
            <label for="active">Activo:</label><br>
            <select name="active" id="active" required>
                <option value="1" {{ old('active') === '1' ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('active') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <button type="submit">Crear Método de Pago</button>
    </form>

    <a href="{{ route('payment-methods.index') }}">Volver a la lista</a>
@endsection
