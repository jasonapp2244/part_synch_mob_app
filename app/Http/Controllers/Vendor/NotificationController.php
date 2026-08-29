<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

/**
 * Vendor-side notifications.
 *
 * Notification rows carry a vendor_id, and orders write one, but the only
 * notification endpoints were behind role:3 — so a vendor had no way to read
 * the notifications addressed to them. This is the vendor half of that.
 */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('vendor_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'status' => true,
            'message' => 'Notifications fetched successfully.',
            'data' => $notifications,
            'unread_count' => $this->unread($request->user()->id),
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'status' => true,
            'unread_count' => $this->unread($request->user()->id),
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate(['notification_id' => 'required|exists:notifications,id']);

        $notification = Notification::where('id', $request->notification_id)
            ->where('vendor_id', $request->user()->id)
            ->first();

        if (! $notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found.'], 404);
        }

        // 'pending' is what this table uses for "read" — see the user-side
        // controller, which set the same convention.
        $notification->update(['status' => 'pending']);

        return response()->json(['status' => true, 'message' => 'Notification marked as read.']);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('vendor_id', $request->user()->id)
            ->where('status', 'sent')
            ->update(['status' => 'pending']);

        return response()->json(['status' => true, 'message' => 'All notifications marked as read.']);
    }

    private function unread($vendorId): int
    {
        return Notification::where('vendor_id', $vendorId)
            ->where('status', 'sent')
            ->count();
    }
}
