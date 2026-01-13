<?php

namespace App\Http\Controllers\secretary\valenzuela;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ValenzuelaDashboardController extends Controller
{
    public function ValenzuelaDashboardPage()
    {
        return view('secretary.valenzuela.dashboard.index');
    }
}
