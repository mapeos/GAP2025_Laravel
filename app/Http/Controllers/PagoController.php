<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso;

class PagoController extends Controller
{
    /**
     * Resumen de servicios/cursos para el administrador
     */
    public function serviciosResumen()
    {
        $cursos = \App\Models\Curso::with(['pagos'])->get();
        return view('admin.dashboard.pagos.servicios', compact('cursos'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'curso_id' => 'required|exists:cursos,id',
            'metodo_pago' => 'required',
            'tipo_pago' => 'required',
            'meses' => 'nullable|integer|min:1',
        ]);


        try {
            $curso = \App\Models\Curso::findOrFail($request->curso_id);
            $pago = new \App\Models\Pago();
            $pago->nombre = $request->nombre;
            $pago->email = $request->email;
            $pago->curso_id = $curso->id;
            $pago->curso = $curso->titulo;
            // Buscar el método de pago por nombre y asignar el ID
            $paymentMethod = \App\Models\PaymentMethod::where('name', $request->metodo_pago)->first();
            $pago->payment_method_id = $paymentMethod ? $paymentMethod->id : null;
            $pago->tipo_pago = $request->tipo_pago;
            $pago->meses = $request->tipo_pago === 'mensual' ? ($request->meses ?? 1) : null;
            $pago->importe = $curso->precio ?? 0;
            $pago->fecha = now();
            $pago->pendiente = true;
            $pago->id_gasto = null; // Para que no falle si no hay gasto
            $pago->save();
            $pago->refresh(); // Asegura que $pago->id_pago esté disponible

            // Crear la factura automáticamente
            $factura = new \App\Models\Factura();
            $factura->user_id = Auth::id();
            $factura->pago_id = $pago->id_pago;
            $factura->producto = $curso->titulo;
            $factura->importe = $pago->importe;
            $factura->fecha = $pago->fecha;
            $factura->estado = 'pagada';
            $factura->save();

            // Redirigir según el rol
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user && $user->hasRole('Alumno')) {
                return redirect()->route('alumno.pagos.facturas')->with('success', 'Pago y factura registrados correctamente.');
            } else {
                return redirect()->route('admin.pagos.facturas.index')->with('success', 'Pago y factura registrados correctamente.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display the payment methods view.
     */
    public function metodos()
    {
        $cursos = Curso::where('estado', 'activo')->get();
        // Detectar si es alumno y mostrar la vista con layout de alumno
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user && $user->hasRole('Alumno')) {
                return view('payment_methods.index', [
                    'cursos' => $cursos,
                    'alumno' => true
                ])->with('layout', 'template.base-alumno');
            }
        }
        return view('payment_methods.index', compact('cursos'));
    }
}



