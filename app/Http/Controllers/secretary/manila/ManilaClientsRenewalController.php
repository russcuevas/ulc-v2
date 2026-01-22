<?php

namespace App\Http\Controllers\secretary\manila;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManilaClientsRenewalController extends Controller
{
    public function ManilaClientAddRenewalRequest(Request $request)
    {
        $last_loan = DB::table('clients_loans')
            ->where('client_id', $request->client_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($last_loan && $last_loan->payment_status === 'unpaid') {
            return redirect()->back()->with('error', 'Cannot create new renewal. Please advise client to pay the existing loan.');
        }

        $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'loan_from'      => 'required|date',
            'loan_to'        => 'required|date|after_or_equal:loan_from',
            'loan_amount'    => 'required|numeric|min:1',
            'daily'          => 'numeric',
            'loan_terms'     => 'required|numeric',
            'pn_number'      => 'required|string|unique:clients_loans,pn_number',
            'release_number' => 'required|string|unique:clients_loans,release_number'
        ]);

        $secretaryFullname = Auth::user()->fullname;
        $client = DB::table('clients')->where('id', $request->client_id)->first();
        $area = DB::table('areas')
            ->join('clients', 'areas.id', '=', 'clients.area_id')
            ->where('clients.id', $request->client_id)
            ->select('areas.areas_name', 'areas.location_name')
            ->first();

        DB::transaction(function () use ($request, $secretaryFullname, $client, $area) {

            DB::table('clients_loans')->insert([
                'client_id'      => $request->client_id,
                'pn_number'      => $request->pn_number,
                'release_number' => $request->release_number,
                'loan_from'      => $request->loan_from,
                'loan_to'        => $request->loan_to,
                'loan_amount'    => $request->loan_amount,
                'balance'        => $request->loan_amount,
                'daily'          => $request->daily,
                'principal'      => $request->loan_amount,
                'loan_terms'     => $request->loan_terms,
                'loan_status'    => 'renewal',
                'payment_status' => 'unpaid',
                'created_by'     => $secretaryFullname,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            Activity::create([
                'users_id'          => Auth::id(),
                'areas'             => $area->location_name ?? '',
                'role'              => 'secretary',
                'type'              => 'Loan Renewal',
                'description'       => sprintf(
                    '<strong>Secretary %s</strong> from Manila Area created a renewal loan.<br>
                    <span style="font-size: 12px; color: #6c757d;">Client: %s</span><br>
                    <span style="font-size: 12px; color: #6c757d;">In: %s - [%s]</span><br>
                    <span style="font-size: 12px; color: #6c757d;">Loan Amount: ₱%s</span>',
                    $secretaryFullname,
                    $client->fullname ?? 'Unknown Client',
                    $area->location_name ?? 'Unknown Location',
                    $area->areas_name ?? 'Unknown Area',
                    number_format($request->loan_amount, 2)
                ),
                'color'             => 'info',
                'is_read_secretary' => 0,
                'is_read_admin'     => 0,
            ]);
        });

        return back()->with('success', 'Renewal loan created successfully.');
    }
}
