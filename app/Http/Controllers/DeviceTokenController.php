<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Push notification device registration.
 *
 * The app had an in-app notifications list but no way to register a device,
 * so nothing could ever be pushed. The client calls register() after login and
 * whenever FCM rotates the token, and unregister() on logout.
 */
class DeviceTokenController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'max:512'],
            'platform' => ['required', Rule::in(['android', 'ios', 'web'])],
            'device_name' => ['nullable', 'string', 'max:255'],
            'app_version' => ['nullable', 'string', 'max:32'],
        ]);

        // The token is the identity, not (user, token): when a phone is handed
        // to a different account FCM reissues the same token to the new login,
        // so the row has to move rather than duplicate.
        $device = DeviceToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id' => $request->user()->id,
                'platform' => $request->platform,
                'device_name' => $request->device_name,
                'app_version' => $request->app_version,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Device registered for notifications.',
            'data' => $device,
        ]);
    }

    /**
     * Drop a device registration — called on logout so the next person to sign
     * in on that handset does not receive the previous user's notifications.
     */
    public function unregister(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'max:512'],
        ]);

        DeviceToken::where('token', $request->token)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Device unregistered.',
        ]);
    }
}
