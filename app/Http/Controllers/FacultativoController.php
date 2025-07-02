<?php

namespace App\Http\Controllers;

use App\Models\SolicitudCita;
use Illuminate\Http\Request;

class FacultativoController extends Controller
{
    //index
    public function index()
    {
        $citasPendientes = SolicitudCita::where('estado', 'pendiente')->get();
        $eventosMes = SolicitudCita::where('estado', 'pendiente')->count();
        return view('facultativo.home', [ 'citasPendientes' => $citasPendientes, 'eventosMes' => $eventosMes]);
    }
    public function citas()
    {
        $citasConfirmadas = SolicitudCita::where('estado', 'confirmada')->get();
        $citasPendientes = SolicitudCita::where('estado', 'pendiente')->get();
        return view('facultativo.citas', ['citasConfirmadas' => $citasConfirmadas, 'citasPendientes' => $citasPendientes]);
    }
    public function cita()
    {
        return view('facultativo.cita');
    }
    public function newCita()
    {
        return view('facultativo.nuevaCita');
    }
    public function editCita()
    {
        return view('facultativo.editCita');
    }
    public function citasConfirmadas()
    {
        $citasConfirmadas = SolicitudCita::where('estado', 'confirmada')->get();
        return view('facultativo.citasConfirmadas', ['citasConfirmadas' => $citasConfirmadas]);
    }
    public function citasPendientes()
    {
        $citasPendientes = SolicitudCita::where('estado', 'pendiente')->get();
        return view('facultativo.citasPendientes', ['citasPendientes' => $citasPendientes]);
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
    public function editTratamiento()
    {
        return view('facultativo.editTratamiento');
    }
}
