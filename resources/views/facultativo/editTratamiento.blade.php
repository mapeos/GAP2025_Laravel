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
          background-color: #fff;
          color: #22223b;
          border: 1px solid #b5b5b5;
          border-radius: 0.5rem;
          padding: 0.75rem 1rem;
          font-size: 1rem;
          transition: border-color 0.2s, box-shadow 0.2s;
          box-shadow: 0 1px 2px 0 rgba(0,0,0,0.03);
        }
        input[type="text"]:focus, input[type="number"]:focus, textarea:focus, select:focus {
          outline: none;
          border-color: #38b000;
          box-shadow: 0 0 0 2px #38b00033;
          background-color: #f8fff4;
        }
        label {
          color: #22223b;
        }
        ::placeholder {
          color: #adb5bd;
          opacity: 1;
        }
        .form-title {
          color: #38b000;
        }
      </style>
      <h3 class="form-title font-bold text-xl mb-4"><i class="ri-sticky-note-add-line"></i> Editar Tratamiento Médico</h3>
      <div class="row g-4">
        <div class="col-md-6">
          <label class="block text-sm font-medium">Nombre del tratamiento</label>
          <input type="text" name="nombre" value="{{ old('nombre', $tratamiento->nombre ?? '') }}" required placeholder="Ej: Limpieza Dental">
        </div>
        <div class="col-md-6">
          <label class="block text-sm font-medium">Especialidad</label>
          <select name="especialidad_id" required>
            <option value="">Seleccione una especialidad</option>
            @foreach($especialidades as $especialidad)
              <option value="{{ $especialidad->id }}" {{ (old('especialidad_id', $tratamiento->especialidad_id ?? '') == $especialidad->id) ? 'selected' : '' }}>
                {{ $especialidad->nombre }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="block text-sm font-medium">Duración (minutos)</label>
          <input type="number" name="duracion_minutos" min="1" value="{{ old('duracion_minutos', $tratamiento->duracion_minutos ?? '') }}" required placeholder="Ej: 30">
        </div>
        <div class="col-md-6">
          <label class="block text-sm font-medium">Costo (€)</label>
          <input type="number" name="costo" min="0" step="0.01" value="{{ old('costo', $tratamiento->costo ?? '') }}" required placeholder="Ej: 35.00">
        </div>
        <div class="col-12">
          <label class="block text-sm font-medium">Descripción</label>
          <textarea name="descripcion" rows="3" required placeholder="Describe el tratamiento...">{{ old('descripcion', $tratamiento->descripcion ?? '') }}</textarea>
        </div>
        <div class="col-12">
          <label class="block text-sm font-medium">¿Activo?</label>
          <select name="activo" required>
            <option value="1" {{ old('activo', $tratamiento->activo ?? 1) == 1 ? 'selected' : '' }}>Sí</option>
            <option value="0" {{ old('activo', $tratamiento->activo ?? 1) == 0 ? 'selected' : '' }}>No</option>
          </select>
        </div>
      </div>
      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-outline-success font-semibold px-4 py-2 rounded mb-3 mt-5">
          <i class="ri-save-2-line"></i> Guardar tratamiento
        </button>
      </div>
   </form>
</div>
@endsection