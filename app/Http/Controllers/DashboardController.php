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
            'activeUsers' => User::where('deleted_at', null)->count(),
            'totalNews' => News::count(),
            'publishedNews' => News::where('fecha_publicacion', '<=', Carbon::now())->count(),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
