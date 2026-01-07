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
            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        Auth::loginUsingId($user->id);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard.page')->with('success', 'Logged in successfully!');
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
