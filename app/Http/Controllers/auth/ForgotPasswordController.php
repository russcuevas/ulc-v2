<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $code = rand(100000, 999999);

        $user->update([
            'reset_code' => $code,
            'reset_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::send('emails.reset-code', [
            'code' => $code,
            'name' => $user->fullname,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Password Reset Code - ULC System');
        });

        // Pass email to reset page
        return redirect()->route('password.reset.form')
            ->with('success', 'Reset code sent to your email.')
            ->with('email', $user->email);
    }

    public function showResetForm(Request $request)
    {
        // Pass email if available in session
        $email = session('email');
        return view('auth.reset_password', compact('email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reset_code' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_code', $request->reset_code)
            ->where('reset_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired reset code.'
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Code verified successfully.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'reset_code' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('step', 'password');
        }

        $user = User::where('email', $request->email)
            ->where('reset_code', $request->reset_code)
            ->where('reset_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return back()
                ->withErrors(['Invalid or expired reset code.'])
                ->withInput()
                ->with('step', 'password');
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_expires_at' => null,
        ]);

        return redirect()->route('auth.login.page')
            ->with('success', 'Password reset successful. You may now login.');
    }
}
