<?php

namespace App\Http\Controllers\admin\caloocan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAreaCaloocanClientsController extends Controller
{
    public function AdminAreaCaloocanClientsPage($areaId)
    {
        $area = $this->getRelatedArea($areaId);

        $clients = DB::table('clients')
            ->where('clients.area_id', $areaId)
            ->whereExists(function ($query) {
                $query->from('clients_loans')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where(function ($q) {
                        $q->where(function ($u) {
                            $u->where('payment_status', 'unpaid')
                                ->where('balance', '>', 0);
                        })
                            ->orWhere(function ($p) {
                                $p->where('payment_status', 'paid')
                                    ->where('balance', 0);
                            });
                    });
            })
            ->select('clients.*')
            ->selectSub(function ($query) {
                $query->from('clients_loans')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where('payment_status', 'unpaid')
                    ->where('balance', '>', 0)
                    ->whereDate('loan_to', '<', now());
            }, 'lapsed_loans_count')
            ->selectSub(function ($query) {
                $query->from('clients_loans')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where('payment_status', 'unpaid')
                    ->where('balance', '>', 0);
            }, 'unpaid_loans_count')
            ->get();

        $clients->transform(function ($client) {
            $client->is_lapsed  = $client->lapsed_loans_count > 0 ? 1 : 0;
            $client->is_renewal = $client->unpaid_loans_count == 0 ? 1 : 0;
            return $client;
        });

        $counts = $this->getClientAccountCounts($areaId);

        return view(
            'admin.areas.caloocan.clients.view_clients',
            array_merge(compact('clients', 'area'), $counts)
        );
    }


    public function AdminAreaCaloocanRenewalClientPage($areaId)
    {
        $area = $this->getRelatedArea($areaId);


        $clients = DB::table('clients')
            ->where('clients.area_id', $areaId)
            ->whereExists(function ($query) {
                $query->from('clients_loans')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where('clients_loans.payment_status', 'paid')
                    ->where('clients_loans.balance', 0);
            })
            ->whereNotExists(function ($query) {
                $query->from('clients_loans')
                    ->whereColumn('clients_loans.client_id', 'clients.id')
                    ->where('clients_loans.payment_status', 'unpaid')
                    ->where('clients_loans.balance', '>', 0);
            })
            ->select('clients.*')
            ->get();

        $clients->transform(function ($client) {
            $client->is_renewal = 1;
            $client->is_lapsed = 0;
            return $client;
        });

        $counts = $this->getClientAccountCounts($areaId);

        return view(
            'admin.areas.caloocan.clients.for_renewal_clients',
            array_merge(compact('clients', 'area'), $counts)
        );
    }



    public function AdminAreaCaloocanLapsedClientsPage($areaId)
    {
        $area = $this->getRelatedArea($areaId);


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
            ->select('clients.*')
            ->get();

        $clients->transform(function ($client) {
            $client->is_lapsed = 1;
            $client->is_renewal = 0;
            return $client;
        });

        $counts = $this->getClientAccountCounts($areaId);

        return view(
            'admin.areas.caloocan.clients.lapsed_clients',
            array_merge(compact('clients', 'area'), $counts)
        );
    }

    public function AdminAreaCaloocanActiveClientsPage($areaId)
    {
        $area = $this->getRelatedArea($areaId);


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
            ->select('clients.*')
            ->get();

        $clients->transform(function ($client) {
            $client->is_lapsed = 0;
            $client->is_renewal = 0;
            return $client;
        });

        $counts = $this->getClientAccountCounts($areaId);

        return view(
            'admin.areas.caloocan.clients.active_clients',
            array_merge(compact('clients', 'area'), $counts)
        );
    }

    // TOTAL COUNTS ACCOUNTS
    private function getClientAccountCounts($areaId)
    {
        return [

            'totalCount' => DB::table('clients')
                ->where('clients.area_id', $areaId)
                ->whereExists(function ($query) {
                    $query->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where(function ($q) {
                            $q->where(function ($u) {
                                $u->where('payment_status', 'unpaid')
                                    ->where('balance', '>', 0);
                            })
                                ->orWhere(function ($p) {
                                    $p->where('payment_status', 'paid')
                                        ->where('balance', 0);
                                });
                        });
                })
                ->count(),

            'activeCount' => DB::table('clients')
                ->where('clients.area_id', $areaId)
                ->whereExists(function ($query) {
                    $query->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where('payment_status', 'unpaid')
                        ->where('balance', '>', 0)
                        ->whereDate('loan_to', '>=', now());
                })
                ->whereNotExists(function ($query) {
                    $query->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where('payment_status', 'unpaid')
                        ->where('balance', '>', 0)
                        ->whereDate('loan_to', '<', now());
                })
                ->count(),

            'lapsedCount' => DB::table('clients')
                ->where('clients.area_id', $areaId)
                ->whereExists(function ($query) {
                    $query->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where('payment_status', 'unpaid')
                        ->where('balance', '>', 0)
                        ->whereDate('loan_to', '<', now());
                })
                ->count(),

            'renewalCount' => DB::table('clients')
                ->where('clients.area_id', $areaId)
                ->whereExists(function ($query) {
                    $query->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where('payment_status', 'paid')
                        ->where('balance', 0);
                })
                ->whereNotExists(function ($query) {
                    $query->from('clients_loans')
                        ->whereColumn('clients_loans.client_id', 'clients.id')
                        ->where('payment_status', 'unpaid')
                        ->where('balance', '>', 0);
                })
                ->count(),
        ];
    }

    // PRINT QUERIES
    public function AdminAreaCaloocanLapsedClientsPrint($areaId)
    {
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->select('id', 'areas_name as area_name')
            ->first();

        $month = request('month');
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        $clients = DB::table('clients')
            ->join('clients_loans', 'clients_loans.client_id', '=', 'clients.id')
            ->leftJoin('clients_payments as month_payments', function ($join) use ($startOfMonth, $endOfMonth) {
                $join->on('month_payments.client_loans_id', '=', 'clients_loans.id')
                    ->whereBetween('month_payments.created_at', [$startOfMonth, $endOfMonth]);
            })
            ->where('clients.area_id', $areaId)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('clients_loans.is_lapsed', 1)
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->whereBetween('month_payments.created_at', [$startOfMonth, $endOfMonth])
                            ->where('month_payments.is_lapsed', 1);
                    });
            })
            ->select(
                'clients.fullname',
                'clients_loans.id as client_loans_id',
                'clients_loans.loan_from as release_date',
                'clients_loans.loan_to',
                'clients_loans.loan_amount',
                'clients_loans.balance',
                'clients_loans.updated_at',
                DB::raw('COALESCE(SUM(month_payments.collection),0) as total_collection'),
                DB::raw('1 as is_lapsed')
            )
            ->groupBy(
                'clients.id',
                'clients.fullname',
                'clients_loans.id',
                'clients_loans.loan_from',
                'clients_loans.loan_to',
                'clients_loans.loan_amount',
                'clients_loans.balance',
                'clients_loans.updated_at'
            )
            ->orderBy('clients.fullname')
            ->get();

        return view(
            'admin.areas.caloocan.clients.print.print_lapsed',
            compact('area', 'clients', 'month', 'startOfMonth', 'endOfMonth')
        );
    }

    private function getRelatedArea($areaId)
    {
        $area = DB::table('areas')
            ->where('id', $areaId)
            ->where('location_name', 'Caloocan Area')
            ->first();

        return $area;
    }
}
