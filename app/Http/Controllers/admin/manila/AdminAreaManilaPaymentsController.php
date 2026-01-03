<?php

namespace App\Http\Controllers\admin\manila;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAreaManilaPaymentsController extends Controller
{
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


        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->where('clients_payments.client_area', $areaId)
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

        return view('admin.areas.manila.payments.payments', compact('area', 'collectors', 'payments'));
    }

    public function AdminAreaManilaClientPaymentsRequest(Request $request, $id)
    {
        $due_date = $request->due_date;
        $collector = DB::table('collectors')->where('id', $request->collector)->first();
        $collected_by = $collector ? $collector->fullname : null;

        // Check if payments for this area and date already exist
        $exists = DB::table('clients_payments')
            ->where('client_area', $id)
            ->where('due_date', $due_date)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Payments for this date already created.');
        }

        // Generate new reference number
        $maxRef = DB::table('clients_payments')
            ->where('due_date', $due_date)
            ->max('reference_number');

        $newNumber = $maxRef ? ((int)substr($maxRef, -3) + 1) : 1;
        $reference_number = $due_date . '-' . sprintf("%03d", $newNumber);

        // Get clients with loans that have balance > 0
        $clients = DB::table('clients')
            ->leftJoin('clients_loans', 'clients.id', '=', 'clients_loans.client_id')
            ->where('clients.area_id', $id)
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
                'clients_loans.daily'
            )
            ->get();

        if ($clients->isEmpty()) {
            return redirect()->back()->with('error', 'No clients with due payments (balance > 0) for this day.');
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

        return redirect()->back()->with('success', 'Payments entry successfully.');
    }


    public function AdminAreaManilaClientDailyPaymentsPage($referenceNumber)
    {
        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->where('clients_payments.reference_number', $referenceNumber)
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
            'admin.areas.manila.payments.daily_payments',
            compact('payments', 'referenceNumber')
        );
    }


    public function AdminAreaManilaClientCollectPaymentRequest(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|string',
        ]);

        // Get the payment record
        $payment = DB::table('clients_payments')->where('id', $id)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found!');
        }

        // Get the associated loan
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

        // Update payment
        DB::table('clients_payments')
            ->where('id', $id)
            ->update([
                'collection' => $newCollection,
                'type' => $request->type,
                'updated_at' => now(),
            ]);

        // Update loan balance
        DB::table('clients_loans')
            ->where('id', $loan->id)
            ->update([
                'balance' => $remainingBalance,
                'updated_at' => now(),
                // ✅ Mark as paid if balance is 0
                'payment_status' => $remainingBalance <= 0 ? 'paid' : $loan->payment_status,
            ]);

        return redirect()->back()->with('success', "Payment collected successfully! Remaining balance: ₱" . number_format($remainingBalance, 2));
    }


    public function AdminAreaManilaClientNoPaymentRequest(Request $request, $id)
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
