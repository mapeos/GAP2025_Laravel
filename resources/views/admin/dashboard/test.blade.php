@extends('template.base')
@section('title', 'Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Página Test!')

@section('breadcrumb')
    <li class="breadcrumb-item active"> Test </li> 
@endsection 


@section('content')

<div class="col-12">
    <div class="card">
        <div class="card-header">Cabecera</div>
        <div class="card-body">
            contenido de la tarjeta
        </div>
    </div>
</div>
@endsection
