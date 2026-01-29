<?php

namespace App\Http\Controllers\secretary\fc;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Clients;
use App\Models\ClientsLoans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FCClientsController extends Controller
{
    public function FCClientsPage()
    {
        $user = Auth::user();
        $areas = DB::table('areas')
            ->where('secretary_id', $user->id)
            ->where('location_name', 'Financial Counselor')
            ->orderBy('areas_name')
            ->get();
        $areaIds = $areas->pluck('id');
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
            ->whereIn('clients.area_id', $areaIds)
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

        return view(
            'secretary.fc.client.index',
            compact('clients', 'areas')
        );
    }

    public function FCAddClientRequest(Request $request)
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
            'balance'       => 'required|numeric|min:1',
            'daily'         => 'numeric',
            'loan_terms'    => 'required|numeric',
            'pn_number'     => 'required|string|unique:clients_loans,pn_number',
            'release_number' => 'required|string|unique:clients_loans,release_number'
        ]);

        $secretaryFullname = Auth::user()->fullname;
        $secretaryId = Auth::id();
        $area = DB::table('areas')
            ->select('areas_name', 'location_name')
            ->where('id', $request->area_id)
            ->first();
        DB::transaction(function () use ($request, $secretaryFullname) {

            $clientId = DB::table('clients')->insertGetId([
                'fullname'   => $request->fullname,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'area_id'    => $request->area_id,
                'gender'     => $request->gender,
                'created_by' => $secretaryFullname,
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
                'balance'        => $request->balance,
                'daily'          => $request->daily,
                'principal'      => $request->loan_amount,
                'loan_terms'     => $request->loan_terms,
                'loan_status'    => 'new',
                'payment_status' => 'unpaid',
                'created_by'     => $secretaryFullname,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

        // Notification
        Activity::create([
            'users_id'          => $secretaryId,
            'areas'    => $area->location_name ?? '',
            'role'              => 'secretary',
            'type'              => 'Account Creation',
            'description'       => sprintf(
                '<strong>Secretary %s</strong> from Financial Counselor created a new client and loan.<br>
                <span style="font-size: 12px; color: #6c757d;">
                <span style="font-size: 12px; color: #6c757d;">Client: %s</span><br>
                <span style="font-size: 12px; color: #6c757d;">In: %s - [%s]</span><br>
                </span>',
                $secretaryFullname,
                $request->fullname,
                $area->location_name,
                DB::table('areas')->where('id', $request->area_id)->value('areas_name') ?? 'Unknown Area'
            ),
            'color'             => 'success',
            'is_read_secretary' => 0,
            'is_read_admin'     => 0,
        ]);

        return redirect()->back()->with('success', 'Client successfully created!');
    }

    public function FCEditClientPage($id)
    {
        $user = Auth::user();

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

        $areas = DB::table('areas')
            ->where('secretary_id', $user->id)
            ->where('location_name', 'Financial Counselor')
            ->orderBy('areas_name')
            ->get();

        $loans = DB::table('clients_loans')
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $last_loans = DB::table('clients_loans')
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        return view(
            'secretary.fc.client.edit',
            compact('client', 'areas', 'loans', 'last_loans')
        );
    }

    public function FCUpdateClientRequest(Request $request, $id)
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

    public function FCDeleteClientRequest(string $id)
    {
        $client = Clients::findOrFail($id);
        ClientsLoans::where('client_id', $client->id)->delete();
        $client->delete();
        return redirect()->back()->with('success', 'Client successfully deleted!');
    }
}
