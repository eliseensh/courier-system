<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * List all notifications (read + unread)
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Mark a single notification as read
     */
    public function markOneRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}
