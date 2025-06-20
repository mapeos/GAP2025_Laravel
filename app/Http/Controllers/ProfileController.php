<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $personas = Persona::with('user')->get(); // Obtiene todas las personas con su usuario asociado
        return view('admin.participantes.index', compact('personas'));
    }

    public function show()
    {
        $user = Auth::user();
        $persona = $user->persona ?? new Persona();

        return view('profile.show', compact('user', 'persona'));
    }

    public function showPersona(Persona $persona)
    {
        return view('admin.participantes.show', compact('persona'));
    }

    public function edit()
    {
        $user = Auth::user();
        $persona = $user->persona ?? new Persona();

        return view('profile.edit', compact('user', 'persona'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'email' => 'required|email|unique:personas,email',
            'telefono' => 'nullable|string|max:15',
        ]);

        Persona::create($request->all());

        return redirect()->route('admin.participantes.index')->with('success', 'Participante creado correctamente.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'dni' => 'required|string|max:9|unique:personas,dni,' . Auth::user()->persona?->id,
            'tfno' => 'nullable|string|max:15',
            // Validación de dirección
            'calle' => 'required|string|max:255',
            'numero' => 'nullable|string|max:10',
            'piso' => 'nullable|string|max:10',
            'cp' => 'required|string|max:10',
            'ciudad' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'pais' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $persona = $user->persona ?? new \App\Models\Persona();
        $persona->fill($request->only(['nombre', 'apellido1', 'apellido2', 'dni', 'tfno']));
        $persona->user_id = $user->id;

        // Dirección
        $direccionData = $request->only(['calle', 'numero', 'piso', 'cp', 'ciudad', 'provincia', 'pais']);
        if ($persona->direccion) {
            $persona->direccion->update($direccionData);
            $direccion = $persona->direccion;
        } else {
            $direccion = \App\Models\Direccion::create($direccionData);
            $persona->direccion_id = $direccion->id;
        }

        $persona->save();

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente');
    }
}
