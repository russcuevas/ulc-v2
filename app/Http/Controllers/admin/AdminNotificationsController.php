<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationsController extends Controller
{
    public function AdminNotificationPage()
    {
        $activities = DB::table('activities')
            ->leftJoin('users', 'activities.users_id', '=', 'users.id')
            ->select(
                'activities.*',
                'users.fullname as fullname'
            )
            ->orderBy('is_read_admin', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notifications.index', compact('activities'));
    }

    public function AdminMarkAllAsReadNotifications()
    {
        Activity::where('is_read_admin', 0)
            ->update(['is_read_admin' => 1]);

        return redirect()->back()->with('success', 'Mark all notification as read');
    }

    public function AdminFetchNotifications()
    {
        $notifications = Activity::latest()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'areas' => $n->areas,
                    'type' => $n->type,
                    'description' => $n->description,
                    'color'       => $n->color,
                    'time'        => $n->created_at->diffForHumans(),
                    'is_read_admin'   => $n->is_read_admin,

                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => Activity::latest()
                ->where('is_read_admin', 0)
                ->count(),
        ]);
    }

    public function AdminMarkAsReadNotifications(Request $request)
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
