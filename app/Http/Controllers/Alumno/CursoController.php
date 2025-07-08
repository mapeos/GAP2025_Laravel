<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Diploma;
use Illuminate\Support\Facades\Auth;

class CursoController extends Controller
{
    public function show($id)
    {
        $curso = Curso::findOrFail($id);
        $alumno = Auth::user()->persona;
        $diploma = null;
        if ($alumno) {
            $diploma = Diploma::obtenerParaParticipante($curso->id, $alumno->id);
        }
        return view('alumno.cursos.show', compact('curso', 'diploma'));
    }
} 