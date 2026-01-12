<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Models\Clients;
use App\Models\ClientsLoans;
use App\Models\ClientsPayment;

class AdminDashboardController extends Controller
{
    public function AdminDashboardPage(Request $request)
    {
        $clientsQuery = Clients::query();
        $loansQuery = ClientsLoans::query();
        $paymentsQuery = ClientsPayment::query();

        $from = null;
        $to = null;

        if ($request->from_month && $request->to_month) {
            $from = Carbon::parse($request->from_month)->startOfMonth();
            $to   = Carbon::parse($request->to_month)->endOfMonth();

            $clientsQuery->whereBetween('created_at', [$from, $to]);
            $loansQuery->whereBetween('created_at', [$from, $to]);
            $paymentsQuery->whereBetween('created_at', [$from, $to]);
        }

        /** --------------------
         * ALL AREAS TOTAL
         * ------------------- */
        $overallClients = $clientsQuery->count();
        $overallLoans = $loansQuery->sum('loan_amount');
        $overallCollected = $paymentsQuery->sum('collection');

        /** --------------------
         * GROUPED BY LOCATION_NAME
         * ------------------- */
        $areaStatsQuery = DB::table('areas')
            ->select('areas.location_name')
            ->distinct()
            ->get();

        $areaStats = [];

        foreach ($areaStatsQuery as $area) {

            // Clients
            $clients = DB::table('clients')
                ->join('areas', 'clients.area_id', '=', 'areas.id')
                ->where('areas.location_name', $area->location_name);

            // Loans
            $loans = DB::table('clients_loans')
                ->join('clients', 'clients_loans.client_id', '=', 'clients.id')
                ->join('areas', 'clients.area_id', '=', 'areas.id')
                ->where('areas.location_name', $area->location_name);

            // Payments
            $payments = DB::table('clients_payments')
                ->join('clients', 'clients_payments.client_id', '=', 'clients.id')
                ->join('areas', 'clients.area_id', '=', 'areas.id')
                ->where('areas.location_name', $area->location_name);

            if ($from && $to) {
                $clients->whereBetween('clients.created_at', [$from, $to]);
                $loans->whereBetween('clients_loans.created_at', [$from, $to]);
                $payments->whereBetween('clients_payments.created_at', [$from, $to]);
            }

            $areaStats[] = [
                'name' => $area->location_name,
                'clients' => $clients->count(),
                'loans' => $loans->sum('clients_loans.loan_amount'),
                'collected' => $payments->sum('clients_payments.collection'),
            ];
        }

        return view('admin.dashboard.index', compact(
            'overallClients',
            'overallLoans',
            'overallCollected',
            'areaStats'
        ));
    }
    public function AnalyticsPage(Request $request, $location)
    {
        $from = $request->from_month ? Carbon::parse($request->from_month)->startOfMonth() : null;
        $to = $request->to_month ? Carbon::parse($request->to_month)->endOfMonth() : null;

        $clientsQuery = Clients::query()
            ->join('areas', 'clients.area_id', '=', 'areas.id')
            ->where('areas.location_name', $location);

        $loansQuery = ClientsLoans::query()
            ->join('clients', 'clients_loans.client_id', '=', 'clients.id')
            ->join('areas', 'clients.area_id', '=', 'areas.id')
            ->where('areas.location_name', $location);

        $paymentsQuery = ClientsPayment::query()
            ->join('clients', 'clients_payments.client_id', '=', 'clients.id')
            ->join('areas', 'clients.area_id', '=', 'areas.id')
            ->where('areas.location_name', $location);

        if ($from && $to) {
            $clientsQuery->whereBetween('clients.created_at', [$from, $to]);
            $loansQuery->whereBetween('clients_loans.created_at', [$from, $to]);
            $paymentsQuery->whereBetween('clients_payments.created_at', [$from, $to]);
        }

        // Overall numbers
        $clients = $clientsQuery->count();
        $loans = $loansQuery->sum('loan_amount');
        $collected = $paymentsQuery->sum('collection');

        $loansByMonth = $loansQuery
            ->selectRaw("DATE_FORMAT(clients_loans.created_at, '%Y-%m') as month, SUM(loan_amount) as total")
            ->groupByRaw("DATE_FORMAT(clients_loans.created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(clients_loans.created_at, '%Y-%m') ASC")
            ->get();

        $collectedByMonth = $paymentsQuery
            ->selectRaw("DATE_FORMAT(clients_payments.created_at, '%Y-%m') as month, SUM(collection) as total")
            ->groupByRaw("DATE_FORMAT(clients_payments.created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(clients_payments.created_at, '%Y-%m') ASC")
            ->get();

        $clientsByMonth = $clientsQuery
            ->selectRaw("DATE_FORMAT(clients.created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupByRaw("DATE_FORMAT(clients.created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(clients.created_at, '%Y-%m') ASC")
            ->get();



        // Loan status breakdown
        $loanStatus = $loansQuery
            ->select('loan_status', DB::raw('COUNT(*) as total'))
            ->groupBy('loan_status')
            ->get();

        // Payment status breakdown
        $paymentStatusRaw = $paymentsQuery
            ->select('type', DB::raw('SUM(collection) as total'))
            ->groupBy('type')
            ->get();

        $paymentStatusMap = [];

        foreach ($paymentStatusRaw as $payment) {
            $key = strtoupper(trim($payment->type));
            if (!isset($paymentStatusMap[$key])) {
                $paymentStatusMap[$key] = 0;
            }
            $paymentStatusMap[$key] += $payment->total;
        }

        // Labels & datasets
        $labels = $loansByMonth->pluck('month')->toArray();
        $loansData = $loansByMonth->pluck('total')->toArray();
        $collectedData = $collectedByMonth->pluck('total')->toArray();
        $clientsData = $clientsByMonth->pluck('total')->toArray();

        $loanStatusLabels = $loanStatus->pluck('loan_status')->toArray();
        $loanStatusData = $loanStatus->pluck('total')->toArray();

        $paymentStatusLabels = array_keys($paymentStatusMap);
        $paymentStatusData = array_values($paymentStatusMap);

        return view('admin.dashboard.analytics', compact(
            'location',
            'clients',
            'loans',
            'collected',
            'labels',
            'loansData',
            'collectedData',
            'clientsData',
            'loanStatusLabels',
            'loanStatusData',
            'paymentStatusLabels',
            'paymentStatusData',
            'from',
            'to'
        ));
    }
}
