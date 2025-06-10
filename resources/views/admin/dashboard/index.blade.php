@extends('template.base')
@section('title', 'Dashboard')
@section('title-sidebar', auth()->user()?->name ?? 'Dashboard Admin')
@section('title-page', 'Administración')

@section('breadcrumb')
    <li class="breadcrumb-item "> <a href="#">Forms</a> </li>
    <li class="breadcrumb-item active"> Select Elements </li> 
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

        // Revenue Performance Chart - Weekly sales breakdown by channel
        const revenueCtx = document.getElementById("salesPerformanceChart");
        if (revenueCtx) {
          new Chart(revenueCtx, {
            type: "bar",
            data: {
              labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
              datasets: [
                {
                  label: "Direct Sales",
                  data: [65, 85, 50, 45, 75, 55, 40],
                  backgroundColor: "#0dcaf0",
                },
                {
                  label: "E-commerce",
                  data: [35, 65, 60, 45, 95, 55, 45],
                  backgroundColor: "#0d6efd",
                },
                {
                  label: "Channel Partners",
                  data: [45, 55, 45, 40, 65, 45, 35],
                  backgroundColor: "#e9ecef",
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                x: {
                  stacked: true,
                  grid: {
                    display: false,
                  },
                },
                y: {
                  stacked: true,
                  grid: {
                    borderDash: [2, 4],
                  },
                },
              },
              plugins: {
                legend: {
                  display: false,
                },
              },
            },
          });
        }

        // Business Performance Radar - Multi-dimensional performance metrics
        const businessMetricsCtx = document.getElementById("performanceChart");
        if (businessMetricsCtx) {
          new Chart(businessMetricsCtx, {
            type: "radar",
            data: {
              labels: ["January", "February", "March", "April", "May", "June"],
              datasets: [
                {
                  label: "Revenue",
                  data: [65, 75, 70, 80, 60, 65],
                  fill: true,
                  backgroundColor: "rgba(25, 135, 84, 0.2)",
                  borderColor: "rgba(25, 135, 84, 1)",
                  pointBackgroundColor: "rgba(25, 135, 84, 1)",
                  pointBorderColor: "#fff",
                  pointHoverBackgroundColor: "#fff",
                  pointHoverBorderColor: "rgba(25, 135, 84, 1)",
                },
                {
                  label: "Growth Rate",
                  data: [70, 68, 65, 78, 82, 55],
                  fill: true,
                  backgroundColor: "rgba(13, 110, 253, 0.2)",
                  borderColor: "rgba(13, 110, 253, 1)",
                  pointBackgroundColor: "rgba(13, 110, 253, 1)",
                  pointBorderColor: "#fff",
                  pointHoverBackgroundColor: "#fff",
                  pointHoverBorderColor: "rgba(13, 110, 253, 1)",
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                r: {
                  angleLines: {
                    display: true,
                    color: "rgba(0, 0, 0, 0.1)",
                  },
                  suggestedMin: 0,
                  suggestedMax: 100,
                },
              },
            },
          });
        }
      };
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


