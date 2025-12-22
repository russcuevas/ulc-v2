<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCollectorController extends Controller
{
    public function AdminCollectorPage()
    {
        $collectors = DB::table('collectors')->get();

        $collectorAreas = DB::table('areas')
            ->join('collectors', 'areas.collector_id', '=', 'collectors.id')
            ->select(
                'collectors.id as collector_id',
                'areas.location_name',
                'areas.areas_name'
            )
            ->get()
            ->groupBy('collector_id');

        return view('admin.collector.index', compact('collectors', 'collectorAreas'));
    }

    public function AdminUpdateCollectorRequest(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string',
        ]);

        $data = [
            'fullname' => $request->fullname,
            'updated_by' => 'Admin',
            'updated_at' => now(),
        ];

        DB::table('collectors')
            ->where('id', $id)
            ->update($data);

        return redirect()->back()->with('success', 'Secretary updated successfully.');
    }
}
