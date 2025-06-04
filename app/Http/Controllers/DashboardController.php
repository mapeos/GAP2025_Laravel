<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'activo')->count(),
            'pendingUsers' => User::where('status', 'pendiente')->count(),
            'totalNews' => News::count(),
            'publishedNews' => 0, // No filtrar por status, ya que la columna no existe
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
