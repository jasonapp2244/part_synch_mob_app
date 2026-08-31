<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'message' => 'Notifications fetched successfully.',
            'data' => $notifications,
            'unread_count' => Notification::where('user_id', auth()->id())
                ->where('status', 'sent')
                ->count(),
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate(['notification_id' => 'required|exists:notifications,id']);

        $notification = Notification::where('id', $request->notification_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found.'], 404);
        }

        $notification->update(['status' => 'pending']); // using 'pending' as read status

        return response()->json(['status' => true, 'message' => 'Notification marked as read.']);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('status', 'sent')
            ->update(['status' => 'pending']);

        return response()->json(['status' => true, 'message' => 'All notifications marked as read.']);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('status', 'sent')
            ->count();

        return response()->json(['status' => true, 'unread_count' => $count]);
    }
}
