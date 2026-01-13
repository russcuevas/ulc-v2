<?php

namespace App\Http\Controllers\secretary\manila;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManilaDashboardController extends Controller
{
    public function ManilaDashboardPage()
    {
        return view('secretary.manila.dashboard.index');
    }
}
