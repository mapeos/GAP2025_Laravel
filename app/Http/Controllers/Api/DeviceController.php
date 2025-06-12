<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Store or update the user's device and FCM token.
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|max:255',
            'fcm_token' => 'required|string|max:1024',
            'device_name' => 'nullable|string|max:255',
            'device_os' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'extra_data' => 'nullable|array',
        ]);

        $device = Device::updateOrCreate(
            [
                'user_id'   => $request->user()->id,
                'device_id' => $request->device_id,
            ],
            [
                'fcm_token'   => $request->fcm_token,
                'device_name' => $request->device_name ?? 'Web Browser',
                'device_os'   => $request->device_os ?? 'web',
                'app_version' => $request->app_version ?? 'web-1.0',
                'extra_data'  => $request->extra_data ?? [],
            ]
        );

        return response()->json([
            'message' => 'Device registered successfully.',
            'device' => $device,
        ]);
    }

    /**
     * Delete a device (optional, for logout or cleanup).
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $deleted = Device::where('user_id', $request->user()->id)
                         ->where('device_id', $request->device_id)
                         ->delete();

        return response()->json([
            'message' => $deleted ? 'Device removed.' : 'Device not found.',
        ]);
    }
}
