<?php

namespace App\Http\Controllers\auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function LoginPage()
    {
        return view('auth.login');
    }

    public function LoginRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // admin/secretary

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::loginUsingId($user->id);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard.page')
                    ->with('success', 'Logged in successfully!');
            }

            if ($user->role === 'secretary') {
                $area = DB::table('areas')->where('secretary_id', $user->id)->first();

                if (!$area) {
                    Auth::logout();
                    return back()->with('error', 'No area assigned to this secretary.');
                }

                $secretaryRoutes = [
                    'Manila Area'           => 'secretary.manila.dashboard.page',
                    'Valenzuela Area'       => 'secretary.valenzuela.dashboard.page',
                    'Caloocan Area'         => 'secretary.caloocan.dashboard.page',
                    'Financial Counselor'   => 'secretary.fc.dashboard.page',
                ];

                if (!isset($secretaryRoutes[$area->location_name])) {
                    Auth::logout();
                    return back()->with('error', 'Invalid area assignment.');
                }

                return redirect()->route($secretaryRoutes[$area->location_name])
                    ->with('success', 'Logged in successfully!');
            }
        }

        // collector
        $collector = DB::table('collectors')->where('email', $request->email)->first();

        if ($collector && Hash::check($request->password, $collector->password)) {
            Auth::guard('collector')->loginUsingId($collector->id);

            $area = DB::table('areas')->where('collector_id', $collector->id)->first();

            if (!$area) {
                Auth::guard('collector')->logout();
                return back()->with('error', 'No area assigned to this collector.');
            }

            $collectorRoutes = [
                'Manila Area'           => 'collector.manila.dashboard.page',
                'Valenzuela Area'       => 'collector.valenzuela.dashboard.page',
                'Caloocan Area'         => 'collector.caloocan.dashboard.page',
                'Financial Counselor'   => 'collector.fc.dashboard.page',
            ];

            if (!isset($collectorRoutes[$area->location_name])) {
                Auth::guard('collector')->logout();
                return back()->with('error', 'Invalid area assignment.');
            }

            return redirect()->route($collectorRoutes[$area->location_name])
                ->with('success', 'Logged in successfully!');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email or password');
    }


    public function Logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login.page')->with('success', 'Logged out successfully.');
    }
}
