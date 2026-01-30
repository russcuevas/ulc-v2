<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CollectorAreaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $area)
    {
        if (!Auth::guard('collector')->check()) {
            return redirect()->route('auth.login.page')
                ->with('error', 'Please login first');
        }

        $collector = Auth::guard('collector')->user();

        $routeArea = strtolower($area);

        $locationMap = [
            'manila' => 'Manila Area',
            'caloocan' => 'Caloocan Area',
            'valenzuela' => 'Valenzuela Area',
            'fc' => 'Financial Counselor',
        ];

        if (!isset($locationMap[$routeArea])) {
            abort(403, 'Invalid area.');
        }

        $hasAccess = DB::table('areas')
            ->where('collector_id', $collector->id)
            ->where('location_name', $locationMap[$routeArea])
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()
                ->with('error', 'You are not assigned to this area.');
        }

        return $next($request);
    }
}
