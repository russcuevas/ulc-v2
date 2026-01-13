<?php

namespace App\Http\Controllers\secretary\manila;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManilaClientsController extends Controller
{
    public function ManilaClientsPage()
    {
        return view('secretary.manila.client.index');
    }
}
