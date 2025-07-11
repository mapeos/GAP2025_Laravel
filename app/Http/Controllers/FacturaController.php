<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        // Si el usuario es administrador, mostrar todas las facturas
        // Si el usuario tiene un campo 'role' y es 'Administrador', mostrar todas las facturas
        if (Auth::check() && Auth::user()->role === 'Administrador') {
            $query = Factura::with(['pago.paymentMethod', 'user']);
        } else {
            $query = Factura::with(['pago.paymentMethod', 'user'])->where('user_id', Auth::id());
        }
        if ($request->filled('buscar')) {
            $query->where('producto', $request->buscar);
        }
        if ($request->filled('fecha_inicio')) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }
        $facturas = $query->orderByDesc('fecha')->get();
        return view('admin.dashboard.pagos.facturas', compact('facturas'));
    }

    public function create(Request $request)
    {
        $pago = null;
        if ($request->filled('pago_id')) {
            $pago = \App\Models\Pago::where('id_pago', $request->pago_id)->first();
        } elseif (Auth::check()) {
            $user = Auth::user();
            $pago = \App\Models\Pago::where('email', $user->email)->orderByDesc('fecha')->first();
        } elseif ($request->filled('email')) {
            $pago = \App\Models\Pago::where('email', $request->email)->orderByDesc('fecha')->first();
        }
        return view('facturas.create', compact('pago'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'producto' => 'required|string|max:255',
            'importe' => 'required|numeric',
            'fecha' => 'required|date',
            'estado' => 'required|string',
            'pago_id' => 'required|integer|exists:pagos,id_pago',
        ]);
        $factura = new \App\Models\Factura();
        $factura->user_id = Auth::id();
        $factura->pago_id = $request->pago_id;
        $factura->producto = $request->producto;
        $factura->importe = $request->importe;
        $factura->fecha = $request->fecha;
        $factura->estado = $request->estado;
        $factura->save();
        return redirect()->route('facturas.index')->with('success', 'Factura generada correctamente.');
    }
}
