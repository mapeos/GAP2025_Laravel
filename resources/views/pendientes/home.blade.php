@extends('template.base')

@section('title', 'Cuenta pendiente de validación')
@section('title-sidebar', 'Pendiente')
@section('title-page', 'Cuenta pendiente de validación')

@section('content')
<div class="container mt-5">
    <div class="alert alert-warning text-center">
        <h3>Tu cuenta está pendiente de validación</h3>
        <p>Un administrador debe validar tu cuenta antes de que puedas acceder a la plataforma.</p>
        <p>Por favor, espera a que tu cuenta sea activada. Recibirás una notificación cuando esté lista.</p>
    </div>
</div>
@endsection
