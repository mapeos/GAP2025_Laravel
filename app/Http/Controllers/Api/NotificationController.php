<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;

class NotificationController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/service-account.json'));
        $this->messaging = $factory->createMessaging();
    }

    // Save or update the FCM token for the authenticated user
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'device_os' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $user = $request->user();

        Device::updateOrCreate(
            ['device_id' => $request->device_id, 'user_id' => $user->id],
            [
                'fcm_token' => $request->fcm_token,
                'device_name' => $request->device_name ?? 'Web Browser',
                'device_os' => $request->device_os ?? 'web',
                'app_version' => $request->app_version ?? 'web-1.0',
            ]
        );

        return response()->json(['success' => true]);
    }

    // Admin: Send push notification to selected users (optional, for API use)
    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
        ]);

        $tokens = Device::whereIn('user_id', $request->user_ids)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->unique()
            ->toArray();

        if (empty($tokens)) {
            return response()->json(['message' => 'No FCM tokens found for selected users'], 404);
        }

        $notification = Notification::create($request->title, $request->body);
        $message = CloudMessage::new()->withNotification($notification);

        try {
            $chunks = array_chunk($tokens, 500);
            $responses = [];

            foreach ($chunks as $chunk) {
                $response = $this->messaging->sendMulticast($message, $chunk);
                $responses[] = $response;
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifications sent',
                'responses' => $responses,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send notification', 'details' => $e->getMessage()], 500);
        }
    }
}
