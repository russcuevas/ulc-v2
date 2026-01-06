<?php

namespace App\Http\Controllers\admin\manila;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminAreaManilaClientsHistoryController extends Controller
{
    public function AdminAreaManilaClientsProfilePage($clientId)
    {
        $client = DB::table('clients')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients.id', $clientId)
            ->where('areas.location_name', 'Manila Area')
            ->select('clients.*')
            ->first();

        if (!$client) {
            abort(404, 'Client not found or not in Manila Area');
        }

        $loans = DB::table('clients_loans')
            ->where('client_id', $clientId)
            ->orderBy('loan_from', 'desc')
            ->get();

        return view(
            'admin.areas.manila.clients.client_history',
            compact('client', 'loans')
        );
    }

    public function AdminAreaManilaClientsPrintLoanHistory($clientId)
    {
        $client = DB::table('clients')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients.id', $clientId)
            ->where('areas.location_name', 'Manila Area')
            ->select('clients.*', 'areas.areas_name as area_name')
            ->first();

        $loans = DB::table('clients_loans')
            ->where('client_id', $clientId)
            ->orderBy('loan_from', 'asc')
            ->get();

        return view(
            'admin.areas.manila.clients.print.print_loan_history',
            [
                'client' => $client,
                'loans'  => $loans,
                'area'   => (object) [
                    'area_name' => $client->area_name
                ]
            ]
        );
    }

    public function AdminAreaManilaClientLoanPaymentsPage($loanId)
    {
        // Get loan and ensure it belongs to Manila
        $loan = DB::table('clients_loans')
            ->join('clients', 'clients.id', '=', 'clients_loans.client_id')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients_loans.id', $loanId)
            ->where('areas.location_name', 'Manila Area')
            ->select('clients_loans.*', 'clients.fullname', 'areas.areas_name as area_name')
            ->first();

        if (!$loan) {
            abort(404, 'Loan not found or not in Manila Area');
        }

        // Get payments for this loan
        $payments = DB::table('clients_payments')
            ->where('client_loans_id', $loanId)
            ->orderBy('due_date', 'asc')  // probably ascending by date is better for payment history
            ->get();

        return view('admin.areas.manila.clients.client_payment_history', compact('loan', 'payments'));
    }
}
