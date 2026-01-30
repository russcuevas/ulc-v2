<?php

namespace App\Http\Controllers\collector\manila;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManilaCollectorDashboardController extends Controller
{
    public function ManilaCollectorDashboardPage()
    {
        $collectorId = Auth::guard('collector')->id();

        $areas = DB::table('areas')
            ->where('collector_id', $collectorId)
            ->get();

        return view('collector.manila.dashboard.index', compact('areas'));
    }
}
