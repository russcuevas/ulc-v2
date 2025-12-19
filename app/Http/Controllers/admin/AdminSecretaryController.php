<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSecretaryController extends Controller
{
    public function AdminSecretaryPage()
    {
        return view('admin.secretary.index');
    }
}
