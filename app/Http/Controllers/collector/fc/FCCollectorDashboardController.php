<?php

namespace App\Http\Controllers\collector\fc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FCCollectorDashboardController extends Controller
{
    public function FCCollectorDashboardPage()
    {
        $collectorId = Auth::guard('collector')->id();
        $area = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->where('location_name', 'Financial Counselor')
            ->select('id', 'areas_name as area_name')
            ->first();

        if (!$area) {
            abort(403, 'Unauthorized area access.');
        }

        $areas = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->get();

        return view('collector.fc.dashboard.index', compact('areas', 'area'));
    }
}
