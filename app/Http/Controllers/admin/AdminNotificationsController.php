<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class AdminNotificationsController extends Controller
{
    public function AdminFetchNotifications()
    {
        $notifications = Activity::where('role', 'admin')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'description' => $n->description,
                    'color'       => $n->color,
                    'time'        => $n->created_at->diffForHumans(),
                    'is_read_admin'   => $n->is_read_admin,

                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => Activity::where('role', 'admin')
                ->where('is_read_admin', 0)
                ->count(),
        ]);
    }

    public function AdminMarkAsRead(Request $request)
    {
        $notificationId = $request->id;

        $notification = Activity::where('role', 'admin')
            ->where('id', $notificationId)
            ->first();

        if ($notification && $notification->is_read_admin == 0) {
            $notification->is_read_admin = 1;
            $notification->save();
        }

        return response()->json(['success' => true]);
    }
}
