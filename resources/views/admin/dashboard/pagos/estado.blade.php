@extends('template.base')
@section('title', 'Estado de Pagos')
@section('title-sidebar', auth()->user()?->name ?? 'Dashboard Admin')
@section('title-page', 'Estado de Pagos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Estado de Pagos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Estado de Pagos</h5>
                </div>
                <div class="card-body">
                    <p>Esta página mostrará el estado de los pagos del sistema.</p>
                    <p>Funcionalidad en desarrollo...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 