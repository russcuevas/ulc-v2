<?php

namespace App\Http\Controllers\admin\manila;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminAreaManilaController extends Controller
{
    public function AdminAreaManilaPage()
    {
        $areas = DB::table('areas')
            ->leftJoin('clients', 'clients.area_id', '=', 'areas.id')
            ->where('areas.location_name', 'Manila Area')
            ->select(
                'areas.id',
                'areas.areas_name',
                DB::raw('COUNT(clients.id) as clients_count')
            )
            ->groupBy('areas.id', 'areas.areas_name')
            ->get();

        return view('admin.areas.manila.index', compact('areas'));
    }

    public function AdminAreaManilaPaymentsPage()
    {
        return view('admin.areas.manila.payments');
    }
}
