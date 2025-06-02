@extends('template.base-admin')
@section('title', 'Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Dashboard')

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   
<script>
      document.addEventListener("DOMContentLoaded", function () {
        // Lead Acquisition Chart - Visualizes lead sources distribution
        const leadAcquisitionCtx = document.getElementById("leadSourceChart");
        new Chart(leadAcquisitionCtx, {
          type: "doughnut",
          data: {
            labels: ["Social Media", "Organic Search", "Direct Calls", "Email Campaign"],
            datasets: [
              {
                data: [25, 35, 20, 20],
                backgroundColor: [
                  "#0d6efd", // Primary blue
                  "#198754", // Success green
                  "#0dcaf0", // Info cyan
                  "#ffc107", // Warning yellow
                ],
                borderWidth: 0,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false,
              },
            },
            cutout: "70%",
          },
        });

        // Revenue Performance Chart - Weekly sales breakdown by channel
        const revenueCtx = document.getElementById("salesPerformanceChart");
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

        // Business Performance Radar - Multi-dimensional performance metrics
        const businessMetricsCtx = document.getElementById("performanceChart");
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
      });
    </script>
@endpush


