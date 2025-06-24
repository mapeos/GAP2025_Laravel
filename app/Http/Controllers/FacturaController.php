<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Factura::where('user_id', \Auth::id());
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('producto', 'like', '%'.$request->buscar.'%')
                  ->orWhere('estado', 'like', '%'.$request->buscar.'%');
            });
        }
        if ($request->filled('fecha_inicio')) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }
        $facturas = $query->orderByDesc('fecha')->get();
        return view('facturas.index', compact('facturas'));
    }
}
