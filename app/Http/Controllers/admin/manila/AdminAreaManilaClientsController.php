<?php

namespace App\Http\Controllers\admin\manila;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminAreaManilaClientsController extends Controller
{
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
            ->get();

        return view('admin.areas.manila.client_history', compact('client', 'loans'));
    }
}
