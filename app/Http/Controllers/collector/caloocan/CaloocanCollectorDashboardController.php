<?php

namespace App\Http\Controllers\collector\caloocan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CaloocanCollectorDashboardController extends Controller
{
    public function CaloocanCollectorDashboardPage()
    {
        $collectorId = Auth::guard('collector')->id();
        $area = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->where('location_name', 'Caloocan Area')
            ->select('id', 'areas_name as area_name')
            ->first();

        if (!$area) {
            abort(403, 'Unauthorized area access.');
        }

        $areas = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->get();

        return view('collector.caloocan.dashboard.index', compact('areas', 'area'));
    }
}
