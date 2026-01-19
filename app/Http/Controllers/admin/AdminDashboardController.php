<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        // -------------------------
        // Date filters
        // -------------------------
        $from = $request->from_month
            ? Carbon::parse($request->from_month)->startOfMonth()
            : null;

        $to = $request->to_month
            ? Carbon::parse($request->to_month)->endOfMonth()
            : null;

        // -------------------------
        // Base queries
        // -------------------------
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

        // -------------------------
        // Apply date filters
        // -------------------------
        if ($from && $to) {
            $clientsQuery->whereBetween('clients.created_at', [$from, $to]);
            $loansQuery->whereBetween('clients_loans.created_at', [$from, $to]);
            $paymentsQuery->whereBetween('clients_payments.created_at', [$from, $to]);
        }

        // -------------------------
        // Summary cards
        // -------------------------
        $clients = $clientsQuery->count();
        $loans = $loansQuery->sum('loan_amount');
        $collected = $paymentsQuery->sum('collection');

        // -------------------------
        // Grouped data by month
        // -------------------------
        $loansByMonth = $loansQuery
            ->selectRaw("DATE_FORMAT(clients_loans.created_at, '%Y-%m') as month, SUM(loan_amount) as total")
            ->groupByRaw("DATE_FORMAT(clients_loans.created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(clients_loans.created_at, '%Y-%m')")
            ->get();


        $collectedByMonth = $paymentsQuery
            ->selectRaw("DATE_FORMAT(clients_payments.created_at, '%Y-%m') as month, SUM(collection) as total")
            ->groupByRaw("DATE_FORMAT(clients_payments.created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(clients_payments.created_at, '%Y-%m')")
            ->get();


        $clientsByMonth = $clientsQuery
            ->selectRaw("DATE_FORMAT(clients.created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupByRaw("DATE_FORMAT(clients.created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(clients.created_at, '%Y-%m')")
            ->get();

        // -------------------------
        // 🔥 Generate full month range
        // -------------------------
        $months = [];

        if ($from && $to) {
            $period = CarbonPeriod::create($from, '1 month', $to);
            foreach ($period as $date) {
                $months[] = $date->format('Y-m');
            }
        } else {
            $months = $loansByMonth->pluck('month')->toArray();
        }

        // -------------------------
        // Map DB results
        // -------------------------
        $loansMap = $loansByMonth->pluck('total', 'month')->toArray();
        $collectionsMap = $collectedByMonth->pluck('total', 'month')->toArray();
        $clientsMap = $clientsByMonth->pluck('total', 'month')->toArray();

        // -------------------------
        // Build aligned chart arrays
        // -------------------------
        $labels = $months;
        $loansData = [];
        $collectedData = [];
        $clientsData = [];

        foreach ($months as $month) {
            $loansData[] = $loansMap[$month] ?? 0;
            $collectedData[] = $collectionsMap[$month] ?? 0;
            $clientsData[] = $clientsMap[$month] ?? 0;
        }

        // -------------------------
        // Loan status breakdown
        // -------------------------
        $loanStatus = $loansQuery
            ->select('loan_status', DB::raw('COUNT(*) as total'))
            ->groupBy('loan_status')
            ->get();

        $loanStatusLabels = $loanStatus->pluck('loan_status')->toArray();
        $loanStatusData = $loanStatus->pluck('total')->toArray();

        // -------------------------
        // Payment type breakdown
        // -------------------------
        $paymentStatusRaw = $paymentsQuery
            ->select('type', DB::raw('SUM(collection) as total'))
            ->groupBy('type')
            ->get();

        $paymentStatusMap = [];

        foreach ($paymentStatusRaw as $payment) {
            $key = strtoupper(trim($payment->type));
            $paymentStatusMap[$key] = ($paymentStatusMap[$key] ?? 0) + $payment->total;
        }

        $paymentStatusLabels = array_keys($paymentStatusMap);
        $paymentStatusData = array_values($paymentStatusMap);


        $areas = DB::table('areas')
            ->where('location_name', $location)
            ->orderBy('areas_name')
            ->get();

        $data = [];

        foreach ($areas as $area) {

            // Collector name
            $collector = DB::table('collectors')->where('id', $area->collector_id)->first();
            $collectorName = $collector ? $collector->fullname : 'N/A';

            // Total Loans
            $totalLoans = DB::table('clients_loans')
                ->whereIn('client_id', function ($query) use ($area, $from, $to) {
                    $sub = DB::table('clients')->select('id')->where('area_id', $area->id);
                    if ($from && $to) {
                        $sub->whereBetween('created_at', [$from, $to]);
                    }
                    $query->fromSub($sub, 'subquery')->select('id');
                })
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->sum('loan_amount');

            // Total Clients
            $totalClients = DB::table('clients')
                ->where('area_id', $area->id)
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->count();

            // Total Collected
            $totalCollected = DB::table('clients_payments')
                ->where('client_area', $area->id)
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->sum('collection');

            $data[] = [
                'area' => $area->areas_name,
                'collector' => $collectorName,
                'total_loans' => $totalLoans,
                'total_clients' => $totalClients,
                'total_collected' => $totalCollected,
            ];
        }

        // -------------------------
        // Return view
        // -------------------------
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
            'to',
            'data'
        ));
    }
}
