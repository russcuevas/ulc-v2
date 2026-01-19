<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Models\ClientsLoans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminClientController extends Controller
{
    public function AdminClientPage()
    {
        $locations = DB::table('areas')
            ->select('location_name')
            ->distinct()
            ->orderBy('location_name')
            ->get();
        $areas = DB::table('areas')->get();

        $clients = DB::table('clients')
            ->leftJoin('areas', 'clients.area_id', '=', 'areas.id')
            ->leftJoin('clients_loans as loans', function ($join) {
                $join->on('clients.id', '=', 'loans.client_id')
                    ->whereRaw('loans.id = (
                 SELECT id 
                 FROM clients_loans 
                 WHERE client_id = clients.id 
                 ORDER BY loan_from DESC 
                 LIMIT 1
             )');
            })
            ->select(
                'clients.*',
                'areas.location_name',
                'areas.areas_name',
                'loans.loan_from',
                'loans.loan_to',
                'loans.loan_amount',
                'loans.loan_terms',
                'loans.loan_status',
                'loans.payment_status'
            )
            ->get();


        return view('admin.client.index', compact('locations', 'areas', 'clients'));
    }

    public function AdminAddClientRequest(Request $request)
    {
        $request->validate([
            'fullname'      => 'required|string|max:255',
            'phone'         => 'required|digits:11',
            'address'       => 'required|string|max:255',
            'area_id'       => 'required|exists:areas,id',
            'gender'        => 'required|string',
            'loan_from'     => 'required|date',
            'loan_to'       => 'required|date|after_or_equal:loan_from',
            'loan_amount'   => 'required|numeric|min:1',
            'daily'         => 'numeric',
            'loan_terms'    => 'required|numeric',
            'pn_number'     => 'required|string|unique:clients_loans,pn_number',
            'release_number' => 'required|string|unique:clients_loans,release_number'
        ]);

        $adminFullname = Auth::user()->fullname;

        DB::transaction(function () use ($request, $adminFullname) {

            $clientId = DB::table('clients')->insertGetId([
                'fullname'   => $request->fullname,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'area_id'    => $request->area_id,
                'gender'     => $request->gender,
                'created_by' => $adminFullname,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('clients_loans')->insert([
                'client_id'      => $clientId,
                'pn_number'      => $request->pn_number,
                'release_number' => $request->release_number,
                'loan_from'      => $request->loan_from,
                'loan_to'        => $request->loan_to,
                'loan_amount'    => $request->loan_amount,
                'balance'        => $request->loan_amount,
                'daily'          => $request->daily,
                'principal'      => $request->loan_amount,
                'loan_terms'     => $request->loan_terms,
                'loan_status'    => 'new',
                'payment_status' => 'unpaid',
                'created_by'     => $adminFullname,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Client successfully created!');
    }

    public function AdminEditClientPage($id)
    {
        $client = DB::table('clients')
            ->leftJoin('areas', 'clients.area_id', '=', 'areas.id')
            ->select(
                'clients.*',
                'areas.location_name',
                'areas.areas_name'
            )
            ->where('clients.id', $id)
            ->first();

        if (!$client) {
            abort(404);
        }

        $locations = DB::table('areas')
            ->select('location_name')
            ->distinct()
            ->orderBy('location_name')
            ->get();

        $areas = DB::table('areas')->get();

        $loans = DB::table('clients_loans')
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();


        $last_loans = DB::table('clients_loans')
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.client.edit', compact('client', 'locations', 'areas', 'loans', 'last_loans'));
    }


    public function AdminUpdateClientRequest(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|digits:11',
            'address' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'gender' => 'required|string',
        ]);

        $client = Clients::findOrFail($id);

        $client->update([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'address' => $request->address,
            'area_id' => $request->area_id,
            'gender' => $request->gender,
        ]);

        return redirect()->back()->with('success', 'Client update information successfully!');
    }

    public function AdminDeleteClientRequest(string $id)
    {
        $client = Clients::findOrFail($id);
        ClientsLoans::where('client_id', $client->id)->delete();
        $client->delete();
        return redirect()->back()->with('success', 'Client successfully deleted!');
    }
}
