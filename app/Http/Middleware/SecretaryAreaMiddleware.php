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

        // Role check
        if ($user->role !== 'secretary') {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        // Get assigned area
        $assignedArea = DB::table('areas')
            ->where('secretary_id', $user->id)
            ->value('location_name');

        if (!$assignedArea) {
            Auth::logout();
            return redirect()->route('auth.login.page')
                ->with('error', 'No area assigned.');
        }

        // Normalize route parameter
        $routeArea = strtolower($area);

        // Determine allowed area from DB (SWITCH)
        switch ($assignedArea) {
            case 'Manila Area':
                $allowedArea = 'manila';
                break;

            case 'Caloocan Area':
                $allowedArea = 'caloocan';
                break;

            case 'Valenzuela Area':
                $allowedArea = 'valenzuela';
                break;

            case 'Financial Counselor':
                $allowedArea = 'fc';
                break;

            default:
                Auth::logout();
                return redirect()->route('auth.login.page')
                    ->with('error', 'Invalid area assignment.');
        }

        // Compare
        if ($allowedArea !== $routeArea) {
            return redirect()->back()
                ->with('error', 'You are not assigned to this area.');
        }

        return $next($request);
    }
}
