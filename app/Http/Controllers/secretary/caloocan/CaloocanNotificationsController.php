<?php

namespace App\Http\Controllers\secretary\caloocan;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaloocanNotificationsController extends Controller
{
    public function CaloocanNotificationsPage()
    {
        $activities = DB::table('activities')
            ->leftJoin('users', 'activities.users_id', '=', 'users.id')
            ->where('activities.areas', 'Caloocan Area')
            ->select(
                'activities.*',
                'users.fullname as fullname'
            )
            ->orderBy('is_read_secretary', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('secretary.caloocan.notifications.index', compact('activities'));
    }

    public function CaloocanMarkAllAsReadNotifications()
    {
        Activity::where('is_read_secretary', 0)
            ->update(['is_read_secretary' => 1]);

        return redirect()->back()->with('success', 'Mark all notification as read');
    }

    public function CaloocanFetchNotifications()
    {
        $notifications = Activity::where('areas', 'Caloocan Area')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'areas' => $n->areas,
                    'type' => $n->type,
                    'description' => $n->description,
                    'color' => $n->color,
                    'time' => $n->created_at->diffForHumans(),
                    'is_read_secretary' => $n->is_read_secretary,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Activity::where('role', 'secretary')
                ->where('areas', 'Caloocan Area')
                ->where('is_read_secretary', 0)
                ->count(),
        ]);
    }
}
