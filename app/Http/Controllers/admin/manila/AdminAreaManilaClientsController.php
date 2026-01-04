<?php

namespace App\Http\Controllers\admin\manila;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminAreaManilaClientsController extends Controller
{
    public function AdminAreaManilaClientsPage($areaId)
    {
        $area = DB::table('areas')->where('id', $areaId)->first();

        $clients = DB::table('clients')
            ->where('clients.area_id', $areaId)
            ->select('clients.*')
            ->selectSub(function ($query) {
                $query->from('clients_loans')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where('clients_loans.payment_status', 'unpaid')
                    ->where('clients_loans.balance', '>', 0)
                    ->whereDate('clients_loans.loan_to', '<', now());
            }, 'lapsed_loans_count')
            ->get();

        // Add boolean flag
        $clients->transform(function ($client) {
            $client->is_lapsed = $client->lapsed_loans_count > 0;
            return $client;
        });

        $counts = $this->getClientAccountCounts($areaId);

        return view(
            'admin.areas.manila.clients.view_clients',
            array_merge(compact('clients', 'area'), $counts)
        );
    }


    public function AdminAreaManilaLapsedClientsPage($areaId)
    {
        $area = DB::table('areas')->where('id', $areaId)->first();

        $clients = DB::table('clients')
            ->where('clients.area_id', $areaId)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('clients_loans')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where('clients_loans.payment_status', 'unpaid')
                    ->where('clients_loans.balance', '>', 0)
                    ->whereDate('clients_loans.loan_to', '<', now());
            })
            ->get();

        $counts = $this->getClientAccountCounts($areaId);

        return view(
            'admin.areas.manila.clients.lapsed_clients',
            array_merge(compact('clients', 'area'), $counts)
        );
    }


    public function AdminAreaManilaActiveClientsPage($areaId)
    {
        $area = DB::table('areas')->where('id', $areaId)->first();

        $clients = DB::table('clients')
            ->where('clients.area_id', $areaId)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('clients_loans')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where('clients_loans.payment_status', 'unpaid')
                    ->where('clients_loans.balance', '>', 0)
                    ->whereDate('clients_loans.loan_to', '>=', now());
            })
            ->get();

        $counts = $this->getClientAccountCounts($areaId);

        return view(
            'admin.areas.manila.clients.active_clients',
            array_merge(compact('clients', 'area'), $counts)
        );
    }


    public function AdminAreaManilaClientsProfilePage($clientId)
    {
        $client = DB::table('clients')
            ->where('id', $clientId)
            ->first();

        if (!$client) {
            abort(404, 'Client not found');
        }

        $loans = DB::table('clients_loans')
            ->where('client_id', $clientId)
            ->get();

        return view('admin.areas.manila.clients.client_history', compact('client', 'loans'));
    }

    private function getClientAccountCounts($areaId)
    {
        return [
            'totalCount' => DB::table('clients')
                ->where('area_id', $areaId)
                ->count(),

            'activeCount' => DB::table('clients')
                ->where('clients.area_id', $areaId)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where('clients_loans.payment_status', 'unpaid')
                        ->where('clients_loans.balance', '>', 0)
                        ->whereDate('clients_loans.loan_to', '>=', now());
                })
                ->count(),

            'lapsedCount' => DB::table('clients')
                ->where('clients.area_id', $areaId)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where('clients_loans.payment_status', 'unpaid')
                        ->where('clients_loans.balance', '>', 0)
                        ->whereDate('clients_loans.loan_to', '<', now());
                })
                ->count(),
        ];
    }
}
