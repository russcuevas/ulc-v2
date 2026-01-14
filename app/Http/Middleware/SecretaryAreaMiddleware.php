<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecretaryAreaMiddleware
{
    public function handle(Request $request, Closure $next, $area)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.page');
        }

        $user = Auth::user();

        if ($user->role !== 'secretary') {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

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
            ->where('secretary_id', $user->id)
            ->where('location_name', $locationMap[$routeArea])
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()
                ->with('error', 'You are not assigned to this area.');
        }

        return $next($request);
    }
}
