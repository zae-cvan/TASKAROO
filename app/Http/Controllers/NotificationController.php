<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Show all notifications (mark as read)
    public function index(Request $request)
    {
        $user = $request->user();

        // Get all notifications
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        // Mark unread notifications as read only when visiting notifications page
        $user->unreadNotifications->markAsRead();

        return view('notifications', compact('notifications'));
    }

    // API endpoint: fetch unread notifications (for dropdown & sidebar)
    public function unread(Request $request)
    {
        $user = $request->user();

        $unread = $user->unreadNotifications->map(function($notif) {
            // Calculate time difference in various units
            $now = \Carbon\Carbon::now();
            $diffSeconds = $notif->created_at->diffInSeconds($now);
            $diffMinutes = $notif->created_at->diffInMinutes($now);
            $diffHours = $notif->created_at->diffInHours($now);
            $diffDays = (int) $notif->created_at->diffInDays($now);
            
            // Determine the "when" message based on time elapsed
            if ($diffSeconds < 60) {
                $when = $diffSeconds . ' second' . ($diffSeconds !== 1 ? 's' : '') . ' ago';
            } elseif ($diffMinutes < 60) {
                $when = $diffMinutes . ' minute' . ($diffMinutes !== 1 ? 's' : '') . ' ago';
            } elseif ($diffHours < 24) {
                $when = $diffHours . ' hour' . ($diffHours !== 1 ? 's' : '') . ' ago';
            } elseif ($diffDays < 7) {
                $when = $diffDays . ' day' . ($diffDays !== 1 ? 's' : '') . ' ago';
            } else {
                $when = $notif->created_at->format('M d, Y');
            }
            
            return [
                'id'    => $notif->id,
                'title' => $notif->data['title'] ?? 'No title',
                'when'  => $when,
                'url'   => isset($notif->data['task_id']) ? url('/tasks/'.$notif->data['task_id']) : '#'
            ];
        });

        return response()->json($unread);
    }
}
