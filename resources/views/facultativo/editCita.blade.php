@php
// Datos temporales para el formulario de edición de cita
$cita = (object)[
  'alumno_id' => 123,
  'profesor_id' => 45,
  'motivo' => 'Consulta general',
  'fecha_propuesta' => now()->addDays(2)->format('Y-m-d H:i:s'),
  'estado' => 'pendiente',
  'tipo_sistema' => 'medico',
  'especialidad_id' => 2,
  'tratamiento_id' => 5,
  'duracion_minutos' => 30,
  'costo' => 40.00,
  'fecha_proxima_cita' => now()->addWeeks(1)->format('Y-m-d'),
  'sintomas' => 'Dolor de cabeza y fiebre.',
  'diagnostico' => 'Posible gripe.',
  'observaciones_medicas' => 'Recomendar reposo y líquidos.'
];

// Datos de ejemplo para especialidades y tratamientos
$especialidades = collect([
  (object)['id' => 1, 'nombre' => 'Cardiología'],
  (object)['id' => 2, 'nombre' => 'Medicina General'],
  (object)['id' => 3, 'nombre' => 'Pediatría'],
]);

$tratamientos = collect([
  (object)['id' => 4, 'nombre' => 'Consulta'],
  (object)['id' => 5, 'nombre' => 'Revisión'],
  (object)['id' => 6, 'nombre' => 'Vacunación'],
]);
@endphp
@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Editar Cita')
@section('content')
<div class="container-fluid">
   <form class="space-y-4" method="POST" action="#">
      @csrf
      @method('PUT')
      <style>
        input[type="text"], input[type="date"], input[type="datetime-local"], input[type="number"], textarea, select {
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
        input[type="text"]:focus, input[type="date"]:focus, input[type="datetime-local"]:focus, input[type="number"]:focus, textarea:focus, select:focus {
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
      </style>
      <h3 class="text-success font-bold text-xl mb-4"><i class="ri-sticky-note-add-line"></i> Editar cita médica</h3>
      <div class="row g-4">
        <div class="col-md-6">
          <label class="block text-sm font-medium mt-3">Motivo</label>
          <input type="text" name="motivo" value="{{ old('motivo', $cita->motivo ?? '') }}" required placeholder="Motivo de la cita">
        </div>
        <div class="col-md-6">
          <label class="block text-sm font-medium mt-3">Fecha propuesta</label>
          <input type="datetime-local" name="fecha_propuesta" value="{{ old('fecha_propuesta', isset($cita->fecha_propuesta) ? \Carbon\Carbon::parse($cita->fecha_propuesta)->format('Y-m-d\TH:i') : '') }}" required>
        </div>
        <div class="col-md-6">
          <label class="block text-sm font-medium mt-3">Estado</label>
          <select name="estado" required>
            <option value="pendiente" {{ old('estado', $cita->estado ?? '') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="confirmada" {{ old('estado', $cita->estado ?? '') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
            <option value="rechazada" {{ old('estado', $cita->estado ?? '') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="block text-sm font-medium mt-3">Especialidad</label>
          <select name="especialidad_id">
            <option value="">Seleccione especialidad</option>
            @foreach($especialidades as $especialidad)
              <option value="{{ $especialidad->id }}" {{ old('especialidad_id', $cita->especialidad_id ?? '') == $especialidad->id ? 'selected' : '' }}>
                {{ $especialidad->nombre }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="block text-sm font-medium mt-3">Duración (minutos)</label>
          <input type="number" name="duracion_minutos" min="1" value="{{ old('duracion_minutos', $cita->duracion_minutos ?? '') }}" placeholder="Ej: 60">
        </div>
      </div>
      <div class="mt-4">
        <label class="block text-sm font-medium">Síntomas</label>
        <textarea name="sintomas" rows="2" placeholder="Describa los síntomas">{{ old('sintomas', $cita->sintomas ?? '') }}</textarea>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="btn btn-outline-success font-semibold px-4 py-2 rounded mb-3 mt-5">
          <i class="ri-save-2-line"></i> Guardar cambios
        </button>
      </div>
   </form>
</div>
@endsection