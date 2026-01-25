<?php

namespace App\Http\Controllers\secretary\valenzuela;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Models\ClientsLoans;
use App\Models\ClientsPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValenzuelaDashboardController extends Controller
{
    public function ValenzuelaDashboardPage(Request $request)
    {
        $currentYear = Carbon::now()->year;

        $fromMonth = $request->from_month
            ? Carbon::parse($request->from_month)->startOfMonth()
            : null;

        $toMonth = $request->to_month
            ? Carbon::parse($request->to_month)->endOfMonth()
            : null;

        $ValenzuelaAreas = DB::table('areas')
            ->where('location_name', 'Valenzuela Area')
            ->pluck('id')
            ->toArray();

        $paymentQuery = ClientsPayment::whereIn('client_area', $ValenzuelaAreas);

        if ($fromMonth && $toMonth) {
            $paymentQuery->whereBetween('updated_at', [$fromMonth, $toMonth]);
        } else {
            $paymentQuery->whereYear('updated_at', $currentYear);
        }

        $totalCollected = $paymentQuery->sum('collection');

        $loanQuery = ClientsLoans::whereIn('client_id', function ($query) use ($ValenzuelaAreas) {
            $query->select('id')
                ->from('clients')
                ->whereIn('area_id', $ValenzuelaAreas);
        });

        if ($fromMonth && $toMonth) {
            $loanQuery->whereBetween('created_at', [$fromMonth, $toMonth]);
        } else {
            $loanQuery->whereYear('created_at', $currentYear);
        }

        $totalLoans = $loanQuery->sum('loan_amount');

        $clientQuery = Clients::whereIn('area_id', $ValenzuelaAreas);

        if ($fromMonth && $toMonth) {
            $clientQuery->whereBetween('created_at', [$fromMonth, $toMonth]);
        } else {
            $clientQuery->whereYear('created_at', $currentYear);
        }

        $totalClients = $clientQuery->count();

        $monthlyCollections = $paymentQuery
            ->selectRaw('MONTH(updated_at) as month, SUM(collection) as total')
            ->groupByRaw('MONTH(updated_at)')
            ->orderByRaw('MONTH(updated_at)')
            ->pluck('total', 'month')
            ->toArray();

        if ($fromMonth && $toMonth) {
            $labels = [];
            $collectionsData = [];

            for ($m = $fromMonth->month; $m <= $toMonth->month; $m++) {
                $labels[] = Carbon::create()->month($m)->format('M');
                $collectionsData[] = (float) ($monthlyCollections[$m] ?? 0);
            }
        } else {
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $collectionsData = [];

            for ($m = 1; $m <= 12; $m++) {
                $collectionsData[] = (float) ($monthlyCollections[$m] ?? 0);
            }
        }

        return view('secretary.valenzuela.dashboard.index', compact(
            'labels',
            'collectionsData',
            'currentYear',
            'totalCollected',
            'totalLoans',
            'totalClients'
        ));
    }


    public function ValenzuelaAreasBreakdownSummary(Request $request)
    {
        $fromMonth = $request->from_month
            ? Carbon::parse($request->from_month)->startOfMonth()
            : null;

        $toMonth = $request->to_month
            ? Carbon::parse($request->to_month)->endOfMonth()
            : null;

        $areas = DB::table('areas')
            ->where('location_name', 'Valenzuela Area')
            ->orderBy('areas_name')
            ->get();

        $data = [];

        foreach ($areas as $area) {
            $collector = DB::table('collectors')->where('id', $area->collector_id)->first();

            $paymentQuery = DB::table('clients_payments')->where('client_area', $area->id);
            if ($fromMonth && $toMonth) {
                $paymentQuery->whereBetween('updated_at', [$fromMonth, $toMonth]);
            }
            $totalCollected = $paymentQuery->sum('collection');

            $clientQuery = DB::table('clients')->where('area_id', $area->id);
            if ($fromMonth && $toMonth) {
                $clientQuery->whereBetween('created_at', [$fromMonth, $toMonth]);
            }
            $totalClients = $clientQuery->count();

            $loanQuery = DB::table('clients_loans')
                ->whereIn('client_id', function ($query) use ($area, $fromMonth, $toMonth) {
                    $sub = DB::table('clients')->select('id')->where('area_id', $area->id);
                    if ($fromMonth && $toMonth) {
                        $sub->whereBetween('created_at', [$fromMonth, $toMonth]);
                    }
                    $query->fromSub($sub, 'subquery')->select('id');
                });
            if ($fromMonth && $toMonth) {
                $loanQuery->whereBetween('created_at', [$fromMonth, $toMonth]);
            }
            $totalLoans = $loanQuery->sum('loan_amount');

            $data[] = [
                'area' => $area->areas_name,
                'collector' => $collector ? $collector->fullname : 'N/A',
                'total_loans' => $totalLoans,
                'total_clients' => $totalClients,
                'total_collected' => $totalCollected,
            ];
        }

        return view('secretary.valenzuela.dashboard.areas_summary', compact('data', 'fromMonth', 'toMonth'));
    }
}
