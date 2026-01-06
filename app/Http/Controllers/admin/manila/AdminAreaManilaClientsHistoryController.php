<?php

namespace App\Http\Controllers\admin\manila;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminAreaManilaClientsHistoryController extends Controller
{
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

        return view('admin.areas.manila.clients.client_history', compact('client', 'loans'));
    }

    public function AdminAreaManilaClientsPrintLoanHistory($clientId)
    {
        $client = DB::table('clients')
            ->where('id', $clientId)
            ->first();

        if (!$client) {
            abort(404, 'Client not found');
        }

        $loans = DB::table('clients_loans')
            ->where('client_id', $clientId)
            ->orderBy('loan_from', 'asc')
            ->get();

        $area = DB::table('areas')
            ->where('id', $client->area_id)
            ->select('id', 'areas_name as area_name')
            ->first();

        return view(
            'admin.areas.manila.clients.print.print_loan_history',
            compact('client', 'loans', 'area')
        );
    }
}
