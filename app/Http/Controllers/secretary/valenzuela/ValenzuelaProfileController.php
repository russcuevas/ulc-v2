<?php

namespace App\Http\Controllers\secretary\valenzuela;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ValenzuelaProfileController extends Controller
{
    public function ValenzuelaProfilePage()
    {
        $secretary = Auth::user();
        return view('secretary.valenzuela.profile.index', compact('secretary'));
    }

    public function ValenzuelaUpdateProfile(Request $request)
    {
        $secretary = Auth::user();

        $request->validate([
            'fullname'      => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20',
            'gender'        => 'required|in:male,female',
            'email'         => 'required|email|unique:users,email,' . $secretary->id,
            'password'      => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'fullname'     => $request->fullname,
            'phone_number' => $request->phone_number,
            'gender'       => $request->gender,
            'email'        => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::where('id', $secretary->id)->update($data);

        return back()->with('success', 'Password updated successfully.');
    }
}
