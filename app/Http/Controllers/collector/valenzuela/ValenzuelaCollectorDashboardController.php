<?php

namespace App\Http\Controllers\collector\valenzuela;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ValenzuelaCollectorDashboardController extends Controller
{
    public function ValenzuelaCollectorDashboardPage()
    {
        $collectorId = Auth::guard('collector')->id();

        $areas = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->get();

        return view('collector.valenzuela.dashboard.index', compact('areas'));
    }
}
