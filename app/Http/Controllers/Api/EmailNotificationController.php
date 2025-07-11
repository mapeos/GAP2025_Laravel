<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailNotificationController extends Controller
{
    /**
     * Send email notification to all users or specific users
     */
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'greeting' => 'nullable|string|max:255',
            'body' => 'required|string',
            'action_text' => 'nullable|string|max:100',
            'action_url' => 'nullable|url',
            'footer_text' => 'nullable|string|max:255',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'active_only' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Build user query
            $query = User::query();
            
            if ($request->has('user_ids') && !empty($request->user_ids)) {
                // Send to specific users
                $query->whereIn('id', $request->user_ids);
            } elseif ($request->active_only) {
                // Send to active users only
                $query->where('status', 1);
            }
            
            // Get users with email addresses
            $users = $query->whereNotNull('email')
                          ->where('email', '!=', '')
                          ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found to send notifications to.'
                ], 404);
            }

            // Create the notification
            $notification = new CustomEmailNotification(
                $request->subject,
                $request->greeting ?? 'Hello!',
                $request->body,
                $request->action_text,
                $request->action_url,
                $request->footer_text ?? 'Thank you for using our application!'
            );

            // Send notifications
            $successCount = 0;
            $errors = [];

            foreach ($users as $user) {
                try {
                    $user->notify($notification);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Email notifications sent successfully',
                'data' => [
                    'total_users' => $users->count(),
                    'successful_sends' => $successCount,
                    'failed_sends' => count($errors),
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to a single user
     */
    public function sendToUser(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'greeting' => 'nullable|string|max:255',
            'body' => 'required|string',
            'action_text' => 'nullable|string|max:100',
            'action_url' => 'nullable|url',
            'footer_text' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($userId);

            if (empty($user->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have an email address.'
                ], 400);
            }

            // Create the notification
            $notification = new CustomEmailNotification(
                $request->subject,
                $request->greeting ?? 'Hello!',
                $request->body,
                $request->action_text,
                $request->action_url,
                $request->footer_text ?? 'Thank you for using our application!'
            );

            // Send notification
            $user->notify($notification);

            return response()->json([
                'success' => true,
                'message' => 'Email notification sent successfully',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
