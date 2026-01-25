<?php

namespace App\Http\Controllers\secretary\valenzuela;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValenzuelaNotificationsController extends Controller
{
    public function ValenzuelaNotificationsPage()
    {
        $activities = DB::table('activities')
            ->leftJoin('users', 'activities.users_id', '=', 'users.id')
            ->where('activities.areas', 'Valenzuela Area')
            ->select(
                'activities.*',
                'users.fullname as fullname'
            )
            ->orderBy('is_read_secretary', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('secretary.valenzuela.notifications.index', compact('activities'));
    }

    public function ValenzuelaMarkAllAsReadNotifications()
    {
        Activity::where('is_read_secretary', 0)
            ->update(['is_read_secretary' => 1]);

        return redirect()->back()->with('success', 'Mark all notification as read');
    }

    public function ValenzuelaFetchNotifications()
    {
        $notifications = Activity::where('areas', 'Valenzuela Area')
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
                ->where('areas', 'Valenzuela Area')
                ->where('is_read_secretary', 0)
                ->count(),
        ]);
    }
}
