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

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid email or password');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid email or password');
        }

        Auth::loginUsingId($user->id);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard.page')->with('success', 'Logged in successfully!');
        }

        if ($user->role === 'secretary') {

            $area = DB::table('areas')
                ->where('secretary_id', $user->id)
                ->first();

            if (!$area) {
                Auth::logout();
                return back()->with('error', 'No area assigned to this secretary.');
            }

            switch ($area->location_name) {
                case 'Manila Area':
                    return redirect()->route('secretary.manila.dashboard.page')
                        ->with('success', 'Logged in successfully!');

                case 'Valenzuela Area':
                    return redirect()->route('secretary.valenzuela.dashboard.page')
                        ->with('success', 'Logged in successfully!');

                case 'Caloocan Area':
                    return redirect()->route('secretary.caloocan.dashboard')
                        ->with('success', 'Logged in successfully!');

                default:
                    Auth::logout();
                    return back()->with('error', 'Invalid area assignment.');
            }
        }


        return redirect('/');
    }

    public function Logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login.page')->with('success', 'Logged out successfully.');
    }
}
