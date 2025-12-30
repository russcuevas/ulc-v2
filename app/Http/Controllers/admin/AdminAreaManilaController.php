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
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->select('id', 'areas_name as area_name')
            ->first();

        $collectors = DB::table('collectors')
            ->whereIn('id', function ($query) use ($areaId) {
                $query->select('collector_id')
                    ->from('areas')
                    ->where('id', $areaId);
            })
            ->get();


        $loans = DB::table('clients_loans')
            ->join('clients', 'clients.id', '=', 'clients_loans.client_id')
            ->where('clients.area_id', $areaId)
            ->select(
                'clients.fullname',
                'clients_loans.*'
            )
            ->orderBy('clients_loans.created_at', 'desc')
            ->get();

        return view('admin.areas.manila.payments', compact('area', 'collectors', 'loans'));
    }

    public function AdminAreaManilaClientPaymentsRequest(Request $request, $id)
    {
        $due_date = $request->due_date;
        $collector = DB::table('collectors')->where('id', $request->collector)->first();
        $collected_by = $collector ? $collector->fullname : null;


        $exists = DB::table('clients_payments')
            ->where('client_area', $id)
            ->where('due_date', $due_date)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Payments for this date already created.');
        }

        $maxRef = DB::table('clients_payments')
            ->where('due_date', $due_date)
            ->max('reference_number');

        if ($maxRef) {
            $lastNumber = (int)substr($maxRef, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $reference_number = $due_date . '-' . sprintf("%03d", $newNumber);

        $clients = DB::table('clients')
            ->leftJoin('clients_loans', 'clients.id', '=', 'clients_loans.client_id')
            ->where('clients.area_id', $id)
            ->where(function ($query) use ($due_date) {
                $query->where(function ($q) use ($due_date) {
                    $q->whereDate('clients_loans.loan_from', '<=', $due_date)
                        ->whereDate('clients_loans.loan_to', '>=', $due_date);
                })
                    ->orWhere('clients_loans.payment_status', 'unpaid');
            })
            ->select(
                'clients.id as client_id',
                'clients_loans.id as client_loans_id',
                'clients.area_id as client_area',
                'clients.fullname',
                'clients_loans.payment_status',
                'clients_loans.daily'
            )
            ->get();



        if ($clients->isEmpty()) {
            return redirect()->back()->with('error', 'No clients with that day.');
        }

        foreach ($clients as $client) {
            DB::table('clients_payments')->insert([
                'reference_number' => $reference_number,
                'collected_by'     => $collected_by,
                'due_date'         => $due_date,
                'client_id'        => $client->client_id,
                'client_loans_id'  => $client->client_loans_id,
                'client_area'      => $client->client_area,
                'daily'            => $client->daily,
                'collection'       => null,
                'type'             => null,
                'created_by'       => 'System',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Payments entry successfully created');
    }
}
