<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch user notifications
    public function fetch(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([]);
        }

        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($n) {
                return [
                    'id'      => $n->id,
                    'type'    => $n->type,
                    'title'   => $n->title,
                    'message' => $n->message,
                    'time'    => $n->created_at->diffForHumans(),
                    'is_read' => $n->is_read,
                ];
            });

        return response()->json($notifications);
    }

    // Mark a notification as read
    public function markAsRead($id)
    {
        $notification          = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

}
