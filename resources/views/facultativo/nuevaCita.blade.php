@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Nueva Cita')
@section('content')
<div class="container-fluid">
   <form class="space-y-4">
      <!-- Datos de identificación -->
      <style>
        input[type="text"], input[type="date"], input[type="tel"], textarea {
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
        input[type="text"]:focus, input[type="date"]:focus, input[type="tel"]:focus, textarea:focus {
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
      <div>
      <h3 class="text-green-500 font-bold text-xl mb-4"><i class="ri-sticky-note-add-line"></i> Nueva cita</h3>
        <h2 class="text-lg font-semibold mb-2 mt-3">Datos de Identificación</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-white-700">Nombre completo</label><br>
            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Fecha de nacimiento</label><br>
            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Documento de identidad</label><br>
            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Teléfono</label><br>
            <input type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
        </div>
      </div>

      <!-- Motivo de consulta -->
      <div>
        <label class="block text-sm font-medium text-white-700 mt-5">Motivo de consulta</label><br>
        <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
      </div>

      <!-- Antecedentes -->
      <div>
        <h2 class="text-lg font-semibold mb-2 mt-5">Antecedentes</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-white-700">Enfermedades previas</label><br>
            <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Cirugías u hospitalizaciones</label><br>
            <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Alergias</label><br>
            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Medicamentos actuales</label><br>
            <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
          </div>
        </div>
      </div>

      <!-- Antecedentes familiares y hábitos -->
      <div>
        <label class="block text-sm font-medium text-white-700 mt-5">Antecedentes familiares relevantes</label><br>
        <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-white-700 mt-5">Hábitos (tabaco, alcohol, drogas, actividad física, dieta)</label><br>
        <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
      </div>

      <!-- Exploración física -->
      <div>
        <h2 class="text-lg font-semibold mt-5">Exploración física</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-white-700">Presión arterial</label><br>
            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Frecuencia cardíaca</label><br>
            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Temperatura</label><br>
            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Frecuencia respiratoria</label><br>
            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
          </div>
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-white-700 mt-5">Examen físico (notas adicionales)</label><br>
          <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
        </div>
      </div>

      <!-- Diagnóstico y tratamiento -->
      <div>
        <h2 class="text-lg font-semibold mt-5">Diagnóstico y tratamiento</h2>
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-white-700 ">Diagnóstico o impresión diagnóstica</label><br>
            <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-white-700 mt-5">Plan de tratamiento</label><br>
            <textarea rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border"></textarea>
          </div>
        </div>
      </div>

      <!-- Información adicional -->
      <div>
        <label class="block text-sm font-medium text-white-700 mt-5">Contacto de emergencia</label><br>
        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
      </div>
      <div>
        <label class="block text-sm font-medium text-white-700 mt-5">Número de seguro médico (si aplica)</label><br>
        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
      </div>

      <!-- Consentimiento -->
      <div class="flex items-center">
        <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label class="ml-2 block text-sm text-white-900">Consiento el tratamiento y uso de mis datos según la normativa vigente.</label><br>
      </div>

      <div class="flex justify-end">
      <button type="submit" class="btn btn-outline-success font-semibold px-4 py-2 rounded mb-3 mt-5">
       <i class="ri-save-2-line"></i> Guardar cita
      </button>
      </div>
    </form>
</div>
@endsection