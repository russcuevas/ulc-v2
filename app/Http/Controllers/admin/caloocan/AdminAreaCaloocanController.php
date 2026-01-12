<?php

namespace App\Http\Controllers\admin\caloocan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAreaCaloocanController extends Controller
{
    public function AdminAreaCaloocanPage()
    {
        $areas = DB::table('areas')
            ->leftJoin('clients', 'clients.area_id', '=', 'areas.id')
            ->where('areas.location_name', 'Caloocan Area')
            ->select(
                'areas.id',
                'areas.areas_name',
                DB::raw('COUNT(clients.id) as clients_count')
            )
            ->groupBy('areas.id', 'areas.areas_name')
            ->get();

        return view('admin.areas.caloocan.index', compact('areas'));
    }

    public function AdminAreaCaloocanPaymentsPage()
    {
        return view('admin.areas.caloocan.payments');
    }

    public function AdminAreaCaloocanPrintSalesReports(Request $request)
    {
        $from = Carbon::parse($request->from_date)->startOfDay();
        $to   = Carbon::parse($request->to_date)->endOfDay();

        $query = DB::table('clients_loans')
            ->join('clients', 'clients.id', '=', 'clients_loans.client_id')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('areas.location_name', 'Caloocan Area')
            ->whereBetween('clients_loans.created_at', [$from, $to]);

        if (!$request->all_areas && $request->area_id) {
            $query->where('areas.id', $request->area_id);
        }

        $loans = $query->select(
            'clients_loans.pn_number',
            'clients_loans.loan_status',
            'clients_loans.created_at',
            'clients.fullname',
            'areas.areas_name',
            'clients_loans.loan_from',
            'clients_loans.loan_to',
            'clients_loans.daily',
            'clients_loans.loan_amount'
        )
            ->orderBy('areas.areas_name')
            ->orderBy('clients_loans.created_at')
            ->get();

        return view('admin.areas.caloocan.print.print_sales', [
            'loans'     => $loans,
            'from'      => $from,
            'to'        => $to,
            'allAreas'  => $request->all_areas
        ]);
    }
}
