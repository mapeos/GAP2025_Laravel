@php
// Simulación de datos del formulario (temporal)
$tratamiento = (object)[
  'nombre' => 'Limpieza Dental',
  'especialidad_id' => 2,
  'duracion_minutos' => 30,
  'costo' => 35.00,
  'descripcion' => 'Procedimiento para limpiar los dientes y encías.',
  'activo' => 1,
];
$especialidades = collect([
  (object)['id' => 1, 'nombre' => 'Odontología General'],
  (object)['id' => 2, 'nombre' => 'Ortodoncia'],
  (object)['id' => 3, 'nombre' => 'Periodoncia'],
]);
@endphp

@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Nueva Cita')
@section('content')
<div class="container-fluid">
   <form class="space-y-4" method="POST" action="#">
      @csrf
      @if(isset($tratamiento))
        @method('PUT')
      @endif
      <style>
        input[type="text"], input[type="number"], textarea, select {
          width: 100% !important;
          background-color: #1e293b;
          color: #f1f5f9;
          border: 1px solid #334155;
          border-radius: 0.5rem;
          padding: 0.75rem 1rem;
          font-size: 1rem;
          transition: border-color 0.2s, box-shadow 0.2s;
          box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        }
        input[type="text"]:focus, input[type="number"]:focus, textarea:focus, select:focus {
          outline: none;
          border-color: #2563eb;
          box-shadow: 0 0 0 2px #2563eb33;
          background-color: #0f172a;
        }
        label {
          color: #f1f5f9;
        }
        ::placeholder {
          color: #94a3b8;
          opacity: 1;
        }
      </style>
      <h3 class="text-green-500 font-bold text-xl mb-4"><i class="ri-sticky-note-add-line"></i> {{ isset($tratamiento) ? 'Editar' : 'Nuevo' }} Tratamiento Médico</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Nombre del tratamiento</label>
          <input type="text" name="nombre" value="{{ old('nombre', $tratamiento->nombre ?? '') }}" required placeholder="Ej: Limpieza Dental">
        </div>
        <div>
          <label class="block text-sm font-medium mt-5">Especialidad</label>
          <select name="especialidad_id" required>
            <option value="">Seleccione una especialidad</option>
            @foreach($especialidades as $especialidad)
              <option value="{{ $especialidad->id }}" {{ (old('especialidad_id', $tratamiento->especialidad_id ?? '') == $especialidad->id) ? 'selected' : '' }}>
                {{ $especialidad->nombre }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mt-5">Duración (minutos)</label>
          <input type="number" name="duracion_minutos" min="1" value="{{ old('duracion_minutos', $tratamiento->duracion_minutos ?? '') }}" required placeholder="Ej: 30">
        </div>
        <div>
          <label class="block text-sm font-medium mt-5">Costo (€)</label>
          <input type="number" name="costo" min="0" step="0.01" value="{{ old('costo', $tratamiento->costo ?? '') }}" required placeholder="Ej: 35.00">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium mt-5">Descripción</label>
        <textarea name="descripcion" rows="3" required placeholder="Describe el tratamiento...">{{ old('descripcion', $tratamiento->descripcion ?? '') }}</textarea>
      </div>
      <div>
        <label class="block text-sm font-medium mt-5">¿Activo?</label>
        <select name="activo" required>
          <option value="1" {{ old('activo', $tratamiento->activo ?? 1) == 1 ? 'selected' : '' }}>Sí</option>
          <option value="0" {{ old('activo', $tratamiento->activo ?? 1) == 0 ? 'selected' : '' }}>No</option>
        </select>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="btn btn-outline-success font-semibold px-4 py-2 rounded mb-3 mt-5">
          <i class="ri-save-2-line"></i> Guardar tratamiento
        </button>
      </div>
   </form>
</div>
@endsection