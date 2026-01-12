<?php

namespace App\Http\Controllers\admin\fc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AdminAreaFCPaymentsController extends Controller
{
    public function AdminAreaFCClientPaymentsPage($areaId)
    {
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('location_name', 'Financial Counselor')
            ->select('id', 'areas_name as area_name')
            ->first();

        $collectors = DB::table('collectors')
            ->whereIn('id', function ($query) use ($areaId) {
                $query->select('collector_id')
                    ->from('areas')
                    ->where('id', $areaId);
            })
            ->get();


        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients_payments.client_area', $areaId)
            ->where('areas.location_name', 'Financial Counselor')

            ->select(
                'clients_payments.reference_number',
                DB::raw('MAX(clients_payments.collected_by) as collected_by'),
                DB::raw('MAX(clients_payments.due_date) as due_date'),
                DB::raw('SUM(clients_payments.daily) as daily'),
                DB::raw('SUM(clients_payments.collection) as collection'),
                DB::raw('MAX(clients_loans.payment_status) as payment_status')
            )
            ->groupBy('clients_payments.reference_number')
            ->orderBy('due_date', 'desc')
            ->get();

        return view('admin.areas.fc.payments.payments', compact('area', 'collectors', 'payments'));
    }

    public function AdminAreaFCPrintSummaryCollections(Request $request, $areaId)
    {
        $from = $request->from_date;
        $to = $request->to_date;

        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('location_name', 'Financial Counselor')
            ->select('id', 'areas_name as area_name')
            ->first();

        $payments = DB::table('clients_payments')
            ->join('areas', 'areas.id', '=', 'clients_payments.client_area')
            ->where('clients_payments.client_area', $areaId)
            ->where('areas.location_name', 'Financial Counselor')
            ->whereBetween('due_date', [$from, $to])

            ->select(
                'reference_number',
                'collected_by',

                DB::raw('COUNT(id) as total_accounts'),
                DB::raw('SUM(daily) as active_amount'),
                DB::raw('SUM(collection) as total_collection'),

                DB::raw("COUNT(CASE WHEN type = 'CASH' THEN 1 END) as cash_count"),
                DB::raw("COUNT(CASE WHEN type = 'ADVANCE' THEN 1 END) as advance_count"),
                DB::raw("COUNT(CASE WHEN type = 'GCASH' THEN 1 END) as gcash_count"),
                DB::raw("COUNT(CASE WHEN type = 'CHEQUE' THEN 1 END) as cheque_count"),

                DB::raw("COUNT(CASE WHEN type = 'NO PAYMENT' THEN 1 END) as no_payment_count")
            )
            ->groupBy('reference_number', 'collected_by')
            ->orderBy('reference_number')
            ->get();

        return view(
            'admin.areas.fc.payments.print.print_payments',
            compact('area', 'payments', 'from', 'to')
        );
    }



    public function AdminAreaFCClientPaymentsRequest(Request $request, $id)
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

        $newNumber = $maxRef ? ((int)substr($maxRef, -3) + 1) : 1;
        $reference_number = $due_date . '-' . sprintf("%03d", $newNumber);

        $clients = DB::table('clients')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->leftJoin('clients_loans', 'clients.id', '=', 'clients_loans.client_id')
            ->where('clients.area_id', $id)
            ->where('areas.location_name', 'Financial Counselor')
            ->where('clients_loans.balance', '>', 0)
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
                'clients_loans.daily',
                'clients_loans.loan_from',
                'clients_loans.loan_to'
            )
            ->get();

        if ($clients->isEmpty()) {
            return redirect()->back()->with('error', 'No clients with due payments (balance > 0) for this day.');
        }

        foreach ($clients as $client) {
            $is_lapsed = Carbon::parse($client->loan_to)->lt(now()) ? 1 : 0;

            if ($is_lapsed) {
                DB::table('clients_loans')
                    ->where('id', $client->client_loans_id)
                    ->update(['is_lapsed' => 1]);
            }

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
                'is_lapsed'        => $is_lapsed,
                'created_by'       => 'System',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Payments entry successfully.');
    }

    public function AdminAreaFCClientUpdateCollection(Request $request, $id)
    {
        $request->validate([
            'collection' => 'required|numeric|min:0',
        ]);

        $payment = DB::table('clients_payments')->where('id', $id)->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $loan = DB::table('clients_loans')->where('id', $payment->client_loans_id)->first();

        if (!$loan) {
            return response()->json(['success' => false, 'message' => 'Loan not found'], 404);
        }

        $prevCollection = $payment->collection ?? 0;
        $maxAllowed = $loan->balance + $prevCollection;
        $newCollection = $request->collection > $maxAllowed ? $maxAllowed : $request->collection;
        $difference = $newCollection - $prevCollection;

        DB::table('clients_payments')
            ->where('id', $id)
            ->update([
                'collection' => $newCollection,
                'updated_at' => now()
            ]);

        DB::table('clients_loans')
            ->where('id', $loan->id)
            ->update([
                'balance' => $loan->balance - $difference,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Collection updated successfully',
            'newCollection' => $newCollection,
            'remainingBalance' => $loan->balance - $difference
        ]);
    }


    public function AdminAreaFCClientDailyPaymentsPage($referenceNumber)
    {
        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients_payments.reference_number', $referenceNumber)
            ->where('areas.location_name', 'Financial Counselor')
            ->select(
                'clients_payments.id',
                'clients.fullname',
                'clients_payments.daily',
                'clients_payments.collection',
                'clients_payments.due_date',
                'clients_payments.collected_by',
                'clients_payments.type',
                'clients_loans.loan_amount',
                'clients_loans.balance',
                'clients_loans.payment_status'
            )
            ->get();

        if ($payments->isEmpty()) {
            abort(404, 'No payments found for this reference number');
        }

        return view(
            'admin.areas.fc.payments.daily_payments',
            compact('payments', 'referenceNumber')
        );
    }

    public function AdminAreaFCClientPrintDailyPayments($referenceNumber)
    {
        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients_payments.reference_number', $referenceNumber)
            ->where('areas.location_name', 'Financial Counselor')
            ->select(
                'clients_payments.id',
                'clients.fullname',
                'clients_payments.daily',
                'clients_payments.collection',
                'clients_payments.due_date',
                'clients_payments.collected_by',
                'clients_payments.type',
                'clients_payments.is_lapsed',
                'clients_loans.loan_amount',
                'clients_loans.balance',
                'clients_loans.payment_status',
                'areas.areas_name'
            )
            ->get();

        if ($payments->isEmpty()) {
            abort(404, 'No payments found for this reference number');
        }

        $area = (object)[
            'areas_name' => $payments->first()->areas_name
        ];

        return view(
            'admin.areas.fc.payments.print.print_daily_payments',
            compact('payments', 'referenceNumber', 'area')
        );
    }





    public function AdminAreaFCClientCollectPaymentRequest(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|string',
        ]);

        $payment = DB::table('clients_payments')->where('id', $id)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found!');
        }

        $loan = DB::table('clients_loans')->where('id', $payment->client_loans_id)->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Loan record not found!');
        }

        $currentCollection = $payment->collection ?? 0;
        $newCollection = $currentCollection + $request->amount;

        $remainingBalance = $loan->balance - $request->amount;

        if ($remainingBalance < 0) {
            return redirect()->back()->with('error', 'Amount exceeds remaining balance!');
        }

        DB::table('clients_payments')
            ->where('id', $id)
            ->update([
                'collection' => $newCollection,
                'type' => $request->type,
                'updated_at' => now(),
            ]);

        DB::table('clients_loans')
            ->where('id', $loan->id)
            ->update([
                'balance' => $remainingBalance,
                'updated_at' => now(),
                'payment_status' => $remainingBalance <= 0 ? 'paid' : $loan->payment_status,
            ]);

        if (Carbon::parse($loan->loan_to)->lt(now())) {
            DB::table('clients_loans')
                ->where('id', $loan->id)
                ->update(['is_lapsed' => 1]);
        }

        return redirect()->back()->with('success', "Payment collected successfully!");
    }



    public function AdminAreaFCClientNoPaymentRequest(Request $request, $id)
    {
        DB::table('clients_payments')
            ->where('id', $id)
            ->update([
                'collection' => 0,
                'type' => 'NO PAYMENT',
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Client marked no payment for this day!');
    }
}
