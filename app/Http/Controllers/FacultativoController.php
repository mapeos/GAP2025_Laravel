<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacultativoController extends Controller
{
    //index
    public function index()
    {
        return view('facultativo.home');
    }
    public function citas()
    {
        return view('facultativo.citas');
    }
    public function cita()
    {
        return view('facultativo.cita');
    }
    public function newCita()
    {
        return view('facultativo.nuevaCita');
    }
    public function citasConfirmadas()
    {
        return view('facultativo.citasConfirmadas');
    }
    public function citasPendientes()
    {
        return view('facultativo.citasPendientes');
    }
    public function pacientes()
    {
        return view('facultativo.pacientes');
    }
    public function paciente()
    {
        return view('facultativo.paciente');
    }
    public function tratamientos()
    {
        return view('facultativo.tratamientos');
    }
    public function tratamiento()
    {
        return view('facultativo.tratamiento');
    }
    public function newTratamiento()
    {
        return view('facultativo.nuevoTratamiento');
    }
}
