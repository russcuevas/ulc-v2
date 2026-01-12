<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function AdminProfilePage()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    public function UpdateProfile(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'fullname'      => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20',
            'gender'        => 'required|in:male,female',
            'email'         => 'required|email|unique:users,email,' . $admin->id,
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

        User::where('id', $admin->id)->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
