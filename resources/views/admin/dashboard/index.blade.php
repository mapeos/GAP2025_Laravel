@extends('template.base')
@section('title', 'Dashboard')
@section('title-sidebar', auth()->user()?->name ?? 'Dashboard Admin')
@section('title-page', 'Administración')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('css')
@endpush

@section('content')
    @include('template.partials.content')
@endsection


@push('js')
    <script>
      window.leadSourcesData = @json($leadSources);
      window.initDashboardCharts = function() {
        const leadSources = window.leadSourcesData;
        const total = leadSources.Web + leadSources.API + leadSources.Otro;
        // Evitar división por cero
        const percentWeb = total ? Math.round((leadSources.Web / total) * 100) : 0;
        const percentApi = total ? Math.round((leadSources.API / total) * 100) : 0;
        const percentAdmin = total ? 100 - percentWeb - percentApi : 0;

        // Actualizar los porcentajes en la UI
        document.getElementById('percent-web').textContent = percentWeb + '%';
        document.getElementById('percent-api').textContent = percentApi + '%';
        document.getElementById('percent-otro').textContent = percentAdmin + '%';

        // Gráfica de procedencia de usuarios
        const ctx = document.getElementById('leadSourceChart');
        if (ctx) {
          new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: ['Web', 'API', 'Admin'],
              datasets: [{
                data: [leadSources.Web, leadSources.API, leadSources.Otro],
                backgroundColor: [
                  '#0d6efd', // Web - azul
                  '#198754', // API - verde
                  '#ffc107'  // Admin - amarillo
                ],
                borderWidth: 0,
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
              },
              cutout: '70%',
            },
          });
        }
      // Esperar a que Chart esté disponible antes de inicializar
      function tryInitDashboardCharts() {
        if (typeof Chart === 'undefined') {
          setTimeout(tryInitDashboardCharts, 100);
          return;
        }
        window.initDashboardCharts();
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryInitDashboardCharts);
      } else {
        tryInitDashboardCharts();
      }
      // wire:navigate soporte para recarga de scripts
      document.addEventListener('navigate', function() {
        setTimeout(function() {
          tryInitDashboardCharts();
        }, 100);
      });
    </script>
@endpush


