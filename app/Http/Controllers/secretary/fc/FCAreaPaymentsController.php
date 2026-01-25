<?php

namespace App\Http\Controllers\secretary\fc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FCAreaPaymentsController extends Controller
{
    public function FCClientPaymentsPage($areaId)
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
                DB::raw('MAX(clients_loans.payment_status) as payment_status'),
                DB::raw('MAX(clients_payments.created_at) as created_at'),
                DB::raw('MAX(clients_payments.created_by) as created_by')
            )
            ->groupBy('clients_payments.reference_number')
            ->orderBy('due_date', 'desc')
            ->get();

        return view('secretary.fc.areas.payments.payments', compact('area', 'collectors', 'payments'));
    }

    public function FCPrintSummaryCollections(Request $request, $areaId)
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

                DB::raw('COUNT(clients_payments.id) as total_accounts'),
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
            'secretary.fc.areas.payments.print.payments',
            compact('area', 'payments', 'from', 'to')
        );
    }

    public function FCClientPaymentsRequest(Request $request, $id)
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

        $secretaryFullname = Auth::user()->fullname;
        $secretaryId = Auth::id();

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
                'created_by'       => $secretaryFullname,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $areaName = DB::table('areas')
            ->where('id', $client->client_area)
            ->value('areas_name') ?? 'Unknown Area';

        //Notifications
        DB::table('activities')->insert([
            'users_id'          => $secretaryId,
            'areas'             => 'Financial Counselor',
            'role'              => 'secretary',
            'type'              => 'Payments Entry',
            'description' => sprintf(
                '<strong>Secretary %s</strong> from Financial Counselor added a new payment entry<br>
                <span style="font-size: 12px; color: #6c757d;">In: Financial Counselor - [%s]</span><br>
                <span style="font-size: 12px; color: #6c757d;">With Reference No: %s</span>',
                $secretaryFullname,
                $areaName,
                $reference_number
            ),

            'color'             => 'success',
            'is_read_secretary' => 0,
            'is_read_admin'     => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect()->back()->with('success', 'Payments entry successfully.');
    }


    public function FCClientUpdateCollection(Request $request, $id)
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

        $newCollection = min($request->collection, $maxAllowed);
        $difference = $newCollection - $prevCollection;
        $newBalance = $loan->balance - $difference;
        $paymentStatus = $newBalance <= 0 ? 'paid' : 'unpaid';

        DB::transaction(function () use ($id, $loan, $newCollection, $newBalance, $paymentStatus) {

            DB::table('clients_payments')
                ->where('id', $id)
                ->update([
                    'collection' => $newCollection,
                    'updated_at' => now()
                ]);

            DB::table('clients_loans')
                ->where('id', $loan->id)
                ->update([
                    'balance' => $newBalance,
                    'payment_status' => $paymentStatus,
                    'updated_at' => now()
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Collection updated successfully',
            'newCollection' => $newCollection,
            'remainingBalance' => $newBalance,
            'payment_status' => $paymentStatus
        ]);
    }

    public function FCClientDailyPaymentsPage($referenceNumber)
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
            'secretary.fc.areas.payments.daily_payments',
            compact('payments', 'referenceNumber')
        );
    }

    public function FCClientPrintDailyPayments($referenceNumber)
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
            'secretary.fc.areas.payments.print.print_daily_payments',
            compact('payments', 'referenceNumber', 'area')
        );
    }

    public function FCClientCollectPaymentRequest(Request $request, $id)
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
        $secretaryFullname = Auth::user()->fullname ?? 'Secretary';
        $secretaryId = Auth::id();
        $client = DB::table('clients')->where('id', $loan->client_id)->first();
        $phone_number = $client->phone ?? null;
        $clientFullname = $client->fullname ?? 'Unknown Client';
        $areaId = $payment->client_area ?? 0;
        $areaLocation = DB::table('areas')
            ->where('id', $areaId)
            ->value('location_name') ?? 'Unknown Location';
        $areaName = DB::table('areas')
            ->where('id', $areaId)
            ->value('areas_name') ?? 'Unknown Area';
        $collectorName = $payment->collected_by ?? 'Unknown Collector';
        $type = $request->type;
        $dueDate = Carbon::parse($payment->due_date)->format('F d, Y');
        DB::table('activities')->insert([
            'users_id'          => $secretaryId,
            'areas'             => $areaLocation,
            'role'              => 'secretary',
            'type'              => 'Collected Payments',
            'description'       => sprintf(
                '<strong>Secretary %s</strong> from Financial Counselor collected a payment<br>
                <span style="font-size: 12px; color: #6c757d;">Date: %s</span><br>
                <span style="font-size: 12px; color: #6c757d;">Collector: %s</span><br>
                <span style="font-size: 12px; color: #6c757d;">Client: %s</span><br>
                <span style="font-size: 12px; color: #6c757d;">In: Financial Counselor - [%s]</span><br>
                <span style="font-size: 12px; color: #6c757d;">Payment Type: %s</span><br>
                <span style="font-size: 12px; color: #6c757d;">Amount Collected: ₱%s</span>',
                $secretaryFullname,
                $dueDate,
                $collectorName,
                $clientFullname,
                $areaName,
                ucfirst($type),
                number_format($request->amount, 2)
            ),
            'color'             => 'success',
            'is_read_secretary' => 0,
            'is_read_admin'     => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        if (!empty($client->phone)) {

            $phone_number = preg_replace('/[^0-9]/', '', $client->phone);

            if (preg_match('/^09\d{9}$/', $phone_number)) {
                $phone_number = '63' . substr($phone_number, 1);
            }

            $message = "Magandang araw {$clientFullname}! Ang iyong payment na halagang ₱"
                . number_format($request->amount, 2)
                . " ay natanggap ni {$collectorName} - {$dueDate}. Natitirang balanse: ₱"
                . number_format(max(0, $remainingBalance), 2)
                . ". Maraming salamat po!";

            $ch = curl_init();

            $parameters = [
                'apikey'     => 'b2a42d09e5cd42585fcc90bf1eeff24e',
                'number'     => $phone_number,
                'message'    => $message,
                'sendername' => 'BPTOCEANUS'
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://semaphore.co/api/v4/messages',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($parameters),
                CURLOPT_RETURNTRANSFER => true,
            ]);

            curl_exec($ch);
            curl_close($ch);
        }

        return redirect()->back()->with('success', "Payment collected successfully!");
    }

    public function FCClientRemindPaymentRequest(Request $request, $id)
    {
        $payment = DB::table('clients_payments')->where('id', $id)->first();
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found!');
        }

        $loan = DB::table('clients_loans')->where('id', $payment->client_loans_id)->first();
        if (!$loan) {
            return redirect()->back()->with('error', 'Loan record not found!');
        }

        $client = DB::table('clients')->where('id', $loan->client_id)->first();
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found!');
        }

        $secretaryId = Auth::id();
        $secretaryFullname = Auth::user()->fullname ?? 'Secretary';
        $areaId = $payment->client_area ?? 0;
        $areaLocation = DB::table('areas')
            ->where('id', $areaId)
            ->value('location_name') ?? 'Unknown Location';
        $daily_payment = $loan->daily ?? 0;
        $dueDate = Carbon::parse($payment->due_date)->format('F d, Y');

        DB::table('activities')->insert([
            'users_id'          => $secretaryId,
            'areas'             => $areaLocation,
            'role'              => 'secretary',
            'type'              => 'Payments Reminder',
            'description'       => sprintf(
                '<strong>Secretary %s</strong> from Financial Counselor sent a payment reminder<br>
            <span style="font-size:12px;color:#6c757d;">Client: %s</span><br>
            <span style="font-size:12px;color:#6c757d;">Daily Payment: ₱%s</span><br>
            <span style="font-size:12px;color:#6c757d;">Due Date: %s</span>',
                $secretaryFullname,
                $client->fullname,
                number_format($daily_payment, 2),
                $dueDate
            ),
            'color'             => 'info',
            'is_read_secretary' => 0,
            'is_read_admin'     => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        /* =======================
       SEND SMS REMINDER
    ======================= */
        if (!empty($client->phone)) {

            $phone_number = preg_replace('/[^0-9]/', '', $client->phone);
            if (preg_match('/^09\d{9}$/', $phone_number)) {
                $phone_number = '63' . substr($phone_number, 1);
            }

            $message = "Magandang araw {$client->fullname}! "
                . "Paalala po na wala pa po kaming natatanggap na bayad ngayong araw. "
                . "Ang iyong daily payment ay: ₱" . number_format($daily_payment, 2) . ". "
                . "Due date: {$dueDate}. Maraming salamat po.";

            $ch = curl_init();

            $parameters = [
                'apikey'     => 'b2a42d09e5cd42585fcc90bf1eeff24e',
                'number'     => $phone_number,
                'message'    => $message,
                'sendername' => 'BPTOCEANUS'
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://semaphore.co/api/v4/messages',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($parameters),
                CURLOPT_RETURNTRANSFER => true,
            ]);

            curl_exec($ch);
            curl_close($ch);
        }

        return redirect()->back()->with('success', 'Payment reminder sent successfully!');
    }

    public function FCClientNoPaymentRequest(Request $request, $id)
    {
        $payment = DB::table('clients_payments')->where('id', $id)->first();
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found!');
        }
        $loan = DB::table('clients_loans')->where('id', $payment->client_loans_id)->first();
        if (!$loan) {
            return redirect()->back()->with('error', 'Loan record not found!');
        }

        $client = DB::table('clients')->where('id', $loan->client_id)->first();
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found!');
        }

        DB::table('clients_payments')
            ->where('id', $id)
            ->update([
                'collection' => 0,
                'type'       => 'NO PAYMENT',
                'updated_at' => now(),
            ]);

        $secretaryId = Auth::id();
        $secretaryFullname = Auth::user()->fullname ?? 'Secretary';
        $areaId = $payment->client_area ?? 0;
        $areaLocation = DB::table('areas')
            ->where('id', $areaId)
            ->value('location_name') ?? 'Unknown Location';
        $daily_payment = $loan->daily ?? 0;
        $dueDate = Carbon::parse($payment->due_date)->format('F d, Y');

        DB::table('activities')->insert([
            'users_id'          => $secretaryId,
            'areas'             => $areaLocation,
            'role'              => 'secretary',
            'type'              => 'No Payment',
            'description'       => sprintf(
                '<strong>Secretary %s</strong> from Financial Counselor marked no payment for the client<br>
            <span style="font-size:12px;color:#6c757d;">Client: %s</span><br>
            <span style="font-size:12px;color:#6c757d;">Daily Payment: ₱%s</span><br>
            <span style="font-size:12px;color:#6c757d;">Due Date: %s</span>',
                $secretaryFullname,
                $client->fullname,
                number_format($daily_payment, 2),
                $dueDate
            ),
            'color'             => 'danger',
            'is_read_secretary' => 0,
            'is_read_admin'     => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        /* =======================
       SEND SMS
    ======================= */
        if (!empty($client->phone)) {
            $phone_number = preg_replace('/[^0-9]/', '', $client->phone);
            if (preg_match('/^09\d{9}$/', $phone_number)) {
                $phone_number = '63' . substr($phone_number, 1);
            }

            $message = "Magandang araw {$client->fullname}! "
                . "Wala po kaming natanggap na bayad ngayong araw ang iyong daily ay (₱" . number_format($daily_payment, 2) . "). "
                . "para sa araw na {$dueDate}. Maraming salamat po!";

            $ch = curl_init();

            $parameters = [
                'apikey'     => 'b2a42d09e5cd42585fcc90bf1eeff24e',
                'number'     => $phone_number,
                'message'    => $message,
                'sendername' => 'BPTOCEANUS'
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://semaphore.co/api/v4/messages',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($parameters),
                CURLOPT_RETURNTRANSFER => true,
            ]);

            curl_exec($ch);
            curl_close($ch);
        }

        return redirect()->back()->with('success', 'Client marked no payment for this day!');
    }
}
