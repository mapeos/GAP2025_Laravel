<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagoController extends Controller
{
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
            'curso' => 'required|string|max:255',
            'metodo_pago' => 'required',
            'tipo_pago' => 'required',
            'meses' => 'nullable|integer|min:1',
        ]);

        try {
            $pago = new \App\Models\Pago();
            $pago->nombre = $request->nombre;
            $pago->email = $request->email;
            $pago->curso = $request->curso;
            $pago->concepto = $request->metodo_pago;
            $pago->tipo_pago = $request->tipo_pago;
            $pago->meses = $request->tipo_pago === 'mensual' ? ($request->meses ?? 1) : null;
            $pago->importe = 0; // Puedes ajustar esto según tu lógica
            $pago->fecha = now();
            $pago->pendiente = true;
            $pago->id_gasto = null; // Para que no falle si no hay gasto
            $pago->save();
            $pago->refresh(); // Asegura que $pago->id_pago esté disponible

            // Crear la factura automáticamente
            $factura = new \App\Models\Factura();
            $factura->user_id = \Auth::id();
            $factura->pago_id = $pago->id_pago;
            $factura->producto = $pago->curso;
            $factura->importe = $pago->importe;
            $factura->fecha = $pago->fecha;
            $factura->estado = 'pagada';
            $factura->save();

            return redirect()->route('facturas.index')->with('success', 'Pago y factura registrados correctamente.');
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
}



