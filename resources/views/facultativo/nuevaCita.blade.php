@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Nueva Cita')
@section('content')
<div class="container-fluid">
  <form class="space-y-4" method="POST" action="#">
    @csrf
    <style>
      input[type="text"],
      input[type="date"],
      input[type="datetime-local"],
      input[type="number"],
      textarea,
      select {
        width: 100% !important;
        background-color: #fff;
        color: #22223b;
        border: 1px solid #b5b5b5;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
      }

      input[type="text"]:focus,
      input[type="date"]:focus,
      input[type="datetime-local"]:focus,
      input[type="number"]:focus,
      textarea:focus,
      select:focus {
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
    <h3 class="text-success font-bold text-xl mb-4"><i class="ri-sticky-note-add-line"></i> Nueva cita médica</h3>
    <div class="row g-4">
      <div class="col-md-6">
        <label class="block text-sm font-medium mt-3">Motivo</label>
        <input type="text" name="motivo" required placeholder="Motivo de la cita">
      </div>
      <div class="col-md-6">
        <label class="block text-sm font-medium mt-3">Fecha propuesta</label>
        <input type="datetime-local" name="fecha_propuesta" required>
      </div>
      <div class="col-md-6">
        <label class="block text-sm font-medium mt-3">Estado</label>
        <select name="estado" required>
          <option value="pendiente">Pendiente</option>
          <option value="confirmada">Confirmada</option>
          <option value="rechazada">Rechazada</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="block text-sm font-medium mt-3">Especialidad</label>
        <select name="especialidad_id">
          <option value="">Seleccione especialidad</option>
          <!-- Opciones dinámicas desde el backend -->
        </select>
      </div>
      <div class="col-md-6">
        <label class="block text-sm font-medium mt-3">Duración (minutos)</label>
        <input type="number" name="duracion_minutos" min="1" placeholder="Ej: 60">
      </div>
    </div>
    <div class="mt-4">
      <label class="block text-sm font-medium">Síntomas</label>
      <textarea name="sintomas" rows="2" placeholder="Describa los síntomas"></textarea>
    </div>
    <div class="flex justify-end">
      <button type="submit" class="btn btn-outline-success font-semibold px-4 py-2 rounded mb-3 mt-5">
        <i class="ri-save-2-line"></i> Guardar cita
      </button>
    </div>
  </form>
</div>
@endsection