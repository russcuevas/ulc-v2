<?php

namespace App\Http\Controllers\collector\valenzuela;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ValenzuelaCollectorPaymentController extends Controller
{
    public function ValenzuelaCollectorCollectionsPage($areaId)
    {
        $collectorId = Auth::guard('collector')->id();

        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('collector_id', $collectorId)
            ->where('location_name', 'Valenzuela Area')
            ->select('id', 'areas_name as area_name')
            ->first();

        if (!$area) {
            return redirect()->route('collector.valenzuela.dashboard.page')
                ->with('error', 'Unauthorized area access.');
        }

        $payments = DB::table('clients_payments')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->where('clients_payments.client_area', $areaId)
            ->select(
                'clients_payments.reference_number',
                DB::raw('MAX(clients_payments.collected_by) as collected_by'),
                DB::raw('MAX(clients_payments.due_date) as due_date'),
                DB::raw('SUM(clients_payments.daily) as daily'),
                DB::raw('SUM(clients_payments.collection) as collection'),
                DB::raw('MAX(clients_loans.payment_status) as payment_status'),
                DB::raw('MAX(clients_payments.created_at) as created_at'),
                DB::raw('MAX(clients_payments.created_by) as created_by')
            )
            ->groupBy('clients_payments.reference_number')
            ->orderBy('due_date', 'desc')
            ->get();

        return view('collector.valenzuela.collections.index', compact('area', 'payments'));
    }

    public function ValenzuelaCollectorClientPaymentPage($referenceNumber)
    {
        $collectorId = Auth::guard('collector')->id();

        $area = DB::table('clients_payments')
            ->join('areas', 'areas.id', '=', 'clients_payments.client_area')
            ->where('clients_payments.reference_number', $referenceNumber)
            ->where('areas.collector_id', $collectorId)
            ->where('areas.location_name', 'Valenzuela Area')
            ->select('areas.id', 'areas.areas_name as area_name')
            ->first();

        if (!$area) {
            return redirect()->route('collector.valenzuela.dashboard.page')
                ->with('error', 'Unauthorized access to this payment reference.');
        }

        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->where('clients_payments.reference_number', $referenceNumber)
            ->where('clients_payments.client_area', $area->id)
            ->select(
                'clients_payments.id',
                'clients.fullname',
                'clients_payments.daily',
                'clients_payments.collection',
                'clients_payments.due_date',
                'clients_payments.created_by',
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
            'collector.valenzuela.collections.payments',
            compact('payments', 'referenceNumber', 'area')
        );
    }

    public function ValenzuelaCollectorCollectRequest(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type'   => 'required|string',
        ]);

        $collector = Auth::guard('collector')->user();
        $collectorId = $collector->id;

        $payment = DB::table('clients_payments')->where('id', $id)->first();
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found!');
        }

        $authorized = DB::table('areas')
            ->where('id', $payment->client_area)
            ->where('collector_id', $collectorId)
            ->where('location_name', 'Valenzuela Area')
            ->exists();

        if (!$authorized) {
            return redirect()->back()->with('error', 'Unauthorized payment access.');
        }

        $loan = DB::table('clients_loans')->where('id', $payment->client_loans_id)->first();
        if (!$loan) {
            return redirect()->back()->with('error', 'Loan record not found!');
        }

        $newCollection    = ($payment->collection ?? 0) + $request->amount;
        $remainingBalance = $loan->balance - $request->amount;

        if ($remainingBalance < 0) {
            return redirect()->back()->with('error', 'Amount exceeds remaining balance!');
        }

        DB::table('clients_payments')
            ->where('id', $id)
            ->update([
                'collection'   => $newCollection,
                'type'         => $request->type,
                'collected_by' => $collector->fullname,
                'updated_at'   => now(),
            ]);

        DB::table('clients_loans')
            ->where('id', $loan->id)
            ->update([
                'balance'        => $remainingBalance,
                'payment_status' => $remainingBalance <= 0 ? 'paid' : $loan->payment_status,
                'updated_at'     => now(),
            ]);

        if (Carbon::parse($loan->loan_to)->lt(now())) {
            DB::table('clients_loans')
                ->where('id', $loan->id)
                ->update(['is_lapsed' => 1]);
        }

        $client = DB::table('clients')->where('id', $loan->client_id)->first();

        if ($client && !empty($client->phone)) {

            $phone = preg_replace('/[^0-9]/', '', $client->phone);
            if (preg_match('/^09\d{9}$/', $phone)) {
                $phone = '63' . substr($phone, 1);
            }

            $dueDate = Carbon::parse($payment->due_date)->format('F d, Y');

            $message = "Magandang araw {$client->fullname}! Ang iyong payment na halagang ₱"
                . number_format($request->amount, 2)
                . " ay natanggap ni {$collector->fullname} - {$dueDate}. Natitirang balanse: ₱"
                . number_format(max(0, $remainingBalance), 2)
                . ". Maraming salamat po!";

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://semaphore.co/api/v4/messages',
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query([
                    'apikey'     => 'b2a42d09e5cd42585fcc90bf1eeff24e',
                    'number'     => $phone,
                    'message'    => $message,
                    'sendername' => 'BPTOCEANUS',
                ]),
                CURLOPT_RETURNTRANSFER => true,
            ]);

            curl_exec($ch);
            curl_close($ch);
        }

        return redirect()->back()->with('success', 'Payment collected successfully!');
    }
}
