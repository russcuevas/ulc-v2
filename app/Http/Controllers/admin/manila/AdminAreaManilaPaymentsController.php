<?php

namespace App\Http\Controllers\admin\manila;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminAreaManilaPaymentsController extends Controller
{
    public function AdminAreaManilaClientPaymentsPage($areaId)
    {
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('location_name', 'Manila Area')
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
            ->where('areas.location_name', 'Manila Area')

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

        return view('admin.areas.manila.payments.payments', compact('area', 'collectors', 'payments'));
    }

    public function AdminAreaManilaPrintSummaryCollections(Request $request, $areaId)
    {
        $from = $request->from_date;
        $to = $request->to_date;

        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('location_name', 'Manila Area')
            ->select('id', 'areas_name as area_name')
            ->first();

        $payments = DB::table('clients_payments')
            ->join('areas', 'areas.id', '=', 'clients_payments.client_area')
            ->where('clients_payments.client_area', $areaId)
            ->where('areas.location_name', 'Manila Area')
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
            'admin.areas.manila.payments.print.print_payments',
            compact('area', 'payments', 'from', 'to')
        );
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

        $newNumber = $maxRef ? ((int)substr($maxRef, -3) + 1) : 1;
        $reference_number = $due_date . '-' . sprintf("%03d", $newNumber);

        $clients = DB::table('clients')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->leftJoin('clients_loans', 'clients.id', '=', 'clients_loans.client_id')
            ->where('clients.area_id', $id)
            ->where('areas.location_name', 'Manila Area')
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
                'clients_loans.balance',
                'clients_loans.loan_from',
                'clients_loans.loan_to'
            )
            ->get();

        if ($clients->isEmpty()) {
            return redirect()->back()->with('error', 'No clients with due payments (balance > 0) for this day.');
        }

        $adminFullname = Auth::user()->fullname;
        $adminId = Auth::id();

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
                'old_balance'      => $client->balance,
                'collection'       => null,
                'type'             => null,
                'is_lapsed'        => $is_lapsed,
                'created_by'       => $adminFullname,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
        $areaName = DB::table('areas')
            ->where('id', $client->client_area)
            ->value('areas_name') ?? 'Unknown Area';

        //Notifications
        DB::table('activities')->insert([
            'users_id'          => $adminId,
            'areas'             => 'Manila Area',
            'role'              => 'admin',
            'type'              => 'Payments Entry',
            'description' => sprintf(
                '<strong>Admin %s</strong> added a new payment entry<br>
                <span style="font-size: 12px; color: #6c757d;">In: Manila Area - [%s]</span><br>
                <span style="font-size: 12px; color: #6c757d;">With Reference No: %s</span>',
                $adminFullname,
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

    public function AdminAreaManilaClientUpdateCollection(Request $request, $id)
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

        // Calculate new balance
        $newBalance = $loan->balance - $difference;

        // Determine payment status
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



    public function AdminAreaManilaClientDailyPaymentsPage($referenceNumber)
    {
        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients_payments.reference_number', $referenceNumber)
            ->where('areas.location_name', 'Manila Area')
            ->select(
                'clients_payments.id',
                'clients.fullname',
                'clients_payments.daily',
                'clients_payments.collection',
                'clients_payments.old_balance',
                'clients_payments.due_date',
                'clients_payments.is_collected',
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

    public function AdminAreaManilaClientPrintDailyPayments($referenceNumber)
    {
        $payments = DB::table('clients_payments')
            ->join('clients', 'clients.id', '=', 'clients_payments.client_id')
            ->join('clients_loans', 'clients_loans.id', '=', 'clients_payments.client_loans_id')
            ->join('areas', 'areas.id', '=', 'clients.area_id')
            ->where('clients_payments.reference_number', $referenceNumber)
            ->where('areas.location_name', 'Manila Area')
            ->select(
                'clients_payments.id',
                'clients.fullname',
                'clients_payments.daily',
                'clients_payments.old_balance',
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
            'admin.areas.manila.payments.print.print_daily_payments',
            compact('payments', 'referenceNumber', 'area')
        );
    }

    public function AdminAreaManilaCollectAllPayments(Request $request, $reference)
{
    $request->validate([
        'type' => 'required|string',
    ]);

    $payments = DB::table('clients_payments')
        ->where('reference_number', $reference)
        ->where('is_collected', 0)
        ->get();

    if ($payments->isEmpty()) {
        return redirect()->back()->with('error', 'No pending payments found for this reference.');
    }

    $adminFullname = Auth::user()->fullname ?? 'Admin';
    $adminId = Auth::id();

    $collectorName = $payments->first()->collected_by ?? 'Unknown Collector';
    $dueDate = Carbon::parse($payments->first()->due_date)->format('F d, Y');
    $areaId = $payments->first()->client_area ?? 0;

    $areaLocation = DB::table('areas')
        ->where('id', $areaId)
        ->value('location_name') ?? 'Unknown Location';

    $areaName = DB::table('areas')
        ->where('id', $areaId)
        ->value('areas_name') ?? 'Unknown Area';

    $totalCollected = 0;
    $totalClients = 0;

    foreach ($payments as $payment) {

        if (is_null($payment->collection) || $payment->collection <= 0) {
            continue;
        }

        // ✅ ONLY MARK PAYMENT AS COLLECTED
        DB::table('clients_payments')
            ->where('id', $payment->id)
            ->update([
                'type'         => $request->type,
                'is_collected' => 1,
                'updated_at'   => now(),
            ]);

        $totalCollected += $payment->collection;
        $totalClients++;
    }

    DB::table('activities')->insert([
        'users_id'          => $adminId,
        'areas'             => $areaLocation,
        'role'              => 'admin',
        'type'              => 'Collected Payments',
        'description'       => sprintf(
            '<strong>Admin %s</strong> collected payments<br>
            <span style="font-size:12px;color:#6c757d;">Reference No: %s</span><br>
            <span style="font-size:12px;color:#6c757d;">Date: %s</span><br>
            <span style="font-size:12px;color:#6c757d;">Collector: %s</span><br>
            <span style="font-size:12px;color:#6c757d;">Area: Manila Area - [%s]</span><br>
            <span style="font-size:12px;color:#6c757d;">Clients Collected: %d</span><br>
            <span style="font-size:12px;color:#6c757d;">Total Collected: ₱%s</span>',
            $adminFullname,
            $reference,
            $dueDate,
            $collectorName,
            $areaName,
            $totalClients,
            number_format($totalCollected, 2)
        ),
        'color'             => 'success',
        'is_read_secretary' => 0,
        'is_read_admin'     => 0,
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    return redirect()->back()->with(
        'success',
        "All payments for reference {$reference} collected successfully!"
    );
}

    public function AdminAreaManilaRemindPaymentsByReference(Request $request, $reference)
    {
        $payments = DB::table('clients_payments')
            ->where('reference_number', $reference)
            ->where('is_collected', 0)
            ->get();

        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'No pending payments found for this reference.');
        }

        $adminId = Auth::id();
        $adminFullname = Auth::user()->fullname ?? 'Admin';
        $sentCount = 0;

        foreach ($payments as $payment) {
            $loan = DB::table('clients_loans')->where('id', $payment->client_loans_id)->first();
            if (!$loan) continue;

            $client = DB::table('clients')->where('id', $loan->client_id)->first();
            if (!$client) continue;

            // Skip if NO PAYMENT or client already has collection + payment type
            if ($payment->type === 'NO PAYMENT' || (!is_null($payment->collection) && !is_null($payment->type))) {
                continue;
            }


            $areaId = $payment->client_area ?? 0;
            $areaLocation = DB::table('areas')->where('id', $areaId)->value('location_name') ?? 'Unknown Location';
            $daily_payment = $loan->daily ?? 0;
            $dueDate = Carbon::parse($payment->due_date)->format('F d, Y');

            // Log activity
            DB::table('activities')->insert([
                'users_id'          => $adminId,
                'areas'             => $areaLocation,
                'role'              => 'admin',
                'type'              => 'Payment Reminder',
                'description'       => sprintf(
                    '<strong>Admin %s</strong> sent a payment reminder<br>
                <span style="font-size:12px;color:#6c757d;">Client: %s</span><br>
                <span style="font-size:12px;color:#6c757d;">Daily Payment: ₱%s</span><br>
                <span style="font-size:12px;color:#6c757d;">Due Date: %s</span>',
                    $adminFullname,
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

            // Send SMS if client has phone
            if (!empty($client->phone)) {
                $phone_number = preg_replace('/[^0-9]/', '', $client->phone);
                if (strlen($phone_number) == 11 && substr($phone_number, 0, 2) == '09') {
                    $phone_number = '63' . substr($phone_number, 1);
                } elseif (strlen($phone_number) != 12 || substr($phone_number, 0, 2) != '63') {
                    continue;
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

            $sentCount++;
        }

        if ($sentCount === 0) {
            return redirect()->back()->with('error', 'No reminders were sent. All payments are either NO PAYMENT or already collected.');
        }

        return redirect()->back()->with('success', "{$sentCount} payment reminder(s) sent successfully for reference {$reference}!");
    }

    public function AdminAreaManilaNoPaymentAll(Request $request, $reference)
    {
        $payments = DB::table('clients_payments')
            ->where('reference_number', $reference)
            ->where('is_collected', 0)
            ->get();

        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'No pending payments found for this reference.');
        }

        $adminId = Auth::id();
        $adminFullname = Auth::user()->fullname ?? 'Admin';

        foreach ($payments as $payment) {

            if ($payment->type === 'NO PAYMENT') {
                continue;
            }

            $loan = DB::table('clients_loans')->where('id', $payment->client_loans_id)->first();
            if (!$loan) continue;

            $client = DB::table('clients')->where('id', $loan->client_id)->first();
            if (!$client) continue;

            $daily_payment = $loan->daily ?? 0;
            $dueDate = Carbon::parse($payment->due_date)->format('F d, Y');
            $areaId = $payment->client_area ?? 0;
            $areaLocation = DB::table('areas')
                ->where('id', $areaId)
                ->value('location_name') ?? 'Unknown Location';

            // Mark as NO PAYMENT
            DB::table('clients_payments')
                ->where('id', $payment->id)
                ->update([
                    'collection' => 0,
                    'type'       => 'NO PAYMENT',
                    'updated_at' => now(),
                ]);

            // Log activity
            DB::table('activities')->insert([
                'users_id'          => $adminId,
                'areas'             => $areaLocation,
                'role'              => 'admin',
                'type'              => 'No Payment',
                'description'       => sprintf(
                    '<strong>Admin %s</strong> marked no payment for the client<br>
                <span style="font-size:12px;color:#6c757d;">Client: %s</span><br>
                <span style="font-size:12px;color:#6c757d;">Daily Payment: ₱%s</span><br>
                <span style="font-size:12px;color:#6c757d;">Due Date: %s</span>',
                    $adminFullname,
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

            // Optional: Send SMS to client
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
        }

        return redirect()->back()->with('success', 'All pending clients marked as NO PAYMENT!');
    }

}
