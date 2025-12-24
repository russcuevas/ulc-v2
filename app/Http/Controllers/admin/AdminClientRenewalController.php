<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminClientRenewalController extends Controller
{
    public function AdminClientAddRenewalRequest(Request $request)
    {

        $last_loan = DB::table('clients_loans')
            ->where('client_id', $request->client_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($last_loan && $last_loan->payment_status === 'unpaid') {
            return redirect()->back()->with('error', 'Cannot create new renewal. Please advised client to paid the existing loan.');
        }

        $request->validate([
            'client_id'     => 'required',
            'loan_from'     => 'required|date',
            'loan_to'       => 'required|date|after_or_equal:loan_from',
            'loan_amount'   => 'required|numeric|min:1',
            'daily'         => 'numeric',
            'loan_terms'    => 'required|numeric',
            'pn_number'     => 'required|string|unique:clients_loans,pn_number',
            'release_number' => 'required|string|unique:clients_loans,release_number'
        ]);


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
            'created_by'     => 'Admin',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
        return back()->with('success', 'Renewal loan created successfully.');
    }
}
