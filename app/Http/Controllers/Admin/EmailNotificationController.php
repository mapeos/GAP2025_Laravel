<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmailNotificationController extends Controller
{
    /**
     * Display a listing of email notifications.
     */
    public function index()
    {
        // Get statistics
        $totalSent = 0; // You can implement this based on your logging needs
        $thisWeek = 0;  // You can implement this based on your logging needs
        $activeUsers = User::where('status', 1)->count();

        // For now, we'll create a simple empty collection to show the interface
        // In a real implementation, you might want to create a separate model
        // to track sent email notifications or use Laravel's notification table
        $emailNotifications = collect([]);

        return view('admin.email-notifications.index', compact(
            'emailNotifications',
            'totalSent',
            'thisWeek',
            'activeUsers'
        ));
    }

    /**
     * Show the form for creating a new email notification.
     */
    public function create()
    {
        $users = User::with('roles')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('name')
            ->get();

        return view('admin.email-notifications.create', compact('users'));
    }

    /**
     * Store a newly created email notification.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'greeting' => 'nullable|string|max:100',
            'body' => 'required|string',
            'action_text' => 'nullable|string|max:50',
            'action_url' => 'nullable|url',
            'footer_text' => 'nullable|string|max:255',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ], [
            'subject.required' => 'El asunto del email es obligatorio.',
            'body.required' => 'El mensaje del email es obligatorio.',
            'users.required' => 'Debes seleccionar al menos un usuario.',
            'users.min' => 'Debes seleccionar al menos un usuario.',
            'action_url.url' => 'La URL del botón debe ser válida.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario.');
        }

        try {
            // Get selected users
            $users = User::whereIn('id', $request->users)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            if ($users->isEmpty()) {
                return back()
                    ->withInput()
                    ->with('error', 'No se encontraron usuarios válidos con direcciones de email.');
            }

            // Create the notification
            $notification = new CustomEmailNotification(
                subject: $request->subject,
                greeting: $request->greeting ?: '¡Hola!',
                body: $request->body,
                actionText: $request->action_text,
                actionUrl: $request->action_url,
                footerText: $request->footer_text ?: '¡Gracias por usar nuestra aplicación!'
            );

            // Send notifications
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($users as $user) {
                try {
                    $user->notify($notification);
                    $successCount++;
                    
                    Log::info('Email notification sent successfully', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'subject' => $request->subject
                    ]);
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'user' => $user->name . ' (' . $user->email . ')',
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Failed to send email notification', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'subject' => $request->subject,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Prepare success message
            $message = "Notificación enviada exitosamente a {$successCount} usuario(s).";
            
            if ($errorCount > 0) {
                $message .= " {$errorCount} envío(s) fallaron.";
                
                // Log detailed errors for admin review
                Log::warning('Email notification batch completed with errors', [
                    'total_users' => $users->count(),
                    'successful' => $successCount,
                    'failed' => $errorCount,
                    'errors' => $errors,
                    'subject' => $request->subject
                ]);
            }

            return redirect()
                ->route('admin.email-notifications.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Email notification system error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subject' => $request->subject ?? 'Unknown'
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error del sistema: ' . $e->getMessage());
        }
    }

    /**
     * Send a test email to the current admin user.
     */
    public function sendTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'greeting' => 'nullable|string|max:100',
            'body' => 'required|string',
            'action_text' => 'nullable|string|max:50',
            'action_url' => 'nullable|url',
            'footer_text' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (empty($user->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu usuario no tiene una dirección de email configurada.'
                ], 400);
            }

            // Create test notification
            $notification = new CustomEmailNotification(
                subject: '[PRUEBA] ' . $request->subject,
                greeting: $request->greeting ?: '¡Hola!',
                body: $request->body . "\n\n--- Este es un email de prueba ---",
                actionText: $request->action_text,
                actionUrl: $request->action_url,
                footerText: $request->footer_text ?: '¡Gracias por usar nuestra aplicación!'
            );

            // Send test notification
            $user->notify($notification);

            Log::info('Test email notification sent', [
                'admin_user_id' => $user->id,
                'admin_email' => $user->email,
                'subject' => $request->subject
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email de prueba enviado exitosamente a ' . $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Test email notification failed', [
                'admin_user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'subject' => $request->subject ?? 'Unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar email de prueba: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users filtered by role and status (AJAX endpoint).
     */
    public function getUsers(Request $request)
    {
        $query = User::with('roles')
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'status' => $user->status,
                ];
            })
        ]);
    }
}
