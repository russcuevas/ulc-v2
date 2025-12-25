<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function AdminAreaManilaClientsPage($areaId)
    {
        $area = DB::table('areas')->where('id', $areaId)->first();

        $clients = DB::table('clients')
            ->where('area_id', $areaId)
            ->get();

        return view('admin.areas.manila.view_clients', compact('clients', 'area'));
    }

    public function AdminAreaManilaClientsProfilePage($clientId)
    {
        $client = DB::table('clients')
            ->where('id', $clientId)
            ->first();

        if (!$client) {
            abort(404, 'Client not found');
        }

        $loans = DB::table('clients_loans')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.areas.manila.client_history', compact('client', 'loans'));
    }

    public function AdminAreaManilaClientPaymentsPage($areaId)
    {
        $area = DB::table('areas')->where('id', $areaId)->first();

        return view('admin.areas.manila.payments', compact('clients'));
    }
}
