<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Device;
use App\Models\SentNotification;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendFirebaseNotificationJob;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = SentNotification::latest()->paginate(10);

        return view('admin.notificaciones.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('admin.notificaciones.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:255',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $tokens = Device::whereIn('user_id', $request->users)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->unique()
            ->filter()
            ->toArray();

        if (empty($tokens)) {
            return back()->with('warning', 'Ninguno de los usuarios seleccionados tiene tokens de notificación.');
        }

        Log::info('Starting to send Firebase notification', [
            'tokens_count' => count($tokens),
            'tokens' => $tokens
        ]);

        try {
            SendFirebaseNotificationJob::dispatch($tokens, $request->title, $request->body);
            Log::info('Firebase notification sent successfully.');
        } catch (\Exception $e) {
            Log::error('Firebase notification error: ' . $e->getMessage());
            return back()->with('error', 'Error al enviar la notificación: ' . $e->getMessage());
        }

        SentNotification::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_ids' => $request->users,
        ]);

        return redirect()->route('admin.notificaciones.index')->with('success', 'Notificación enviada exitosamente.');
    }
}
