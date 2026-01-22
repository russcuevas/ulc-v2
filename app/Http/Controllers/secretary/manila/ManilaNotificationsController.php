<?php

namespace App\Http\Controllers\secretary\manila;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ManilaNotificationsController extends Controller
{
    public function ManilaFetchNotifications()
    {
        $notifications = Activity::where('areas', 'Manila Area')
            ->where('role', 'secretary') // filter role
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
                ->where('areas', 'Manila Area')
                ->where('is_read_secretary', 0)
                ->count(),
        ]);
    }
}
