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
            'metodo_pago' => 'required',
            'tipo_pago' => 'required',
            'meses' => 'nullable|integer|min:1',
        ]);

        try {
            $pago = new \App\Models\Pago();
            $pago->concepto = $request->metodo_pago;
            $pago->tipo_pago = $request->tipo_pago;
            $pago->meses = $request->tipo_pago === 'mensual' ? ($request->meses ?? 1) : null;
            $pago->importe = 0; // Puedes ajustar esto según tu lógica
            $pago->fecha = now();
            $pago->pendiente = true;
            $pago->id_gasto = null; // Para que no falle si no hay gasto
            $pago->save();
            return redirect()->back()->with('success', 'Pago registrado correctamente.');
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



