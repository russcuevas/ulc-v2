<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSecretaryController extends Controller
{
    public function AdminSecretaryPage()
    {
        $secretaries = DB::table('users')
            ->where('role', 'secretary')
            ->get();

        $areas = DB::table('areas')->get();

        return view('admin.secretary.index', compact('secretaries', 'areas'));
    }

    public function AdminUpdateSecretaryRequest(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'gender' => 'nullable|string',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'updated_at' => now(),
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')
            ->where('id', $id)
            ->update($data);

        return redirect()->back()->with('success', 'Secretary updated successfully.');
    }
}
