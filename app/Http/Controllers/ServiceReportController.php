<?php

namespace App\Http\Controllers;


use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ServiceReportController extends Controller
{

    public function serviceReport(Request $request)
    {
        $reports = Service::selectRaw('DATE(service_date) as tanggal,
                COUNT(*) as total,
                SUM(status="menunggu") as menunggu,
                SUM(status="proses") as proses,
                SUM(status="selesai") as selesai,
                SUM(status="diambil") as diambil')
            ->when($request->filled('month'), function($q) use ($request) {
                $q->whereMonth('service_date', substr($request->month, 5, 2))
                  ->whereYear('service_date', substr($request->month, 0, 4));
            }, function($q) use ($request) {
                $q->whereDate('service_date', $request->date ?? now());
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('reports.service', compact('reports'));
    }

    public function jobReport(Request $request)
    {
        $jobs = Service::with(['vehicle.customer', 'jobs', 'mechanics'])
            ->when($request->filled('date'), fn($q) => $q->whereDate('service_date', $request->date))
            ->when($request->filled('month'), fn($q) => $q->whereYear('service_date', substr($request->month,0,4))
                                                       ->whereMonth('service_date', substr($request->month,5,2)))
            ->get();

        return view('reports.jobs', compact('jobs'));
    }

    public function sparepartReport(Request $request)
    {
        // $spareparts = Service::with(['vehicle', 'spareparts.barang', 'mechanics'])
        //     ->when($request->filled('date'), fn($q) => $q->whereDate('service_date', $request->date))
        //     ->when($request->filled('month'), fn($q) => $q->whereYear('service_date', substr($request->month,0,4))
        //                                                ->whereMonth('service_date', substr($request->month,5,2)))
        //     ->get();

        //     // dd($spareparts->toArray());

        // return view('reports.spareparts', compact('spareparts'));
           $spareparts = Service::with(['vehicle', 'spareparts.barang', 'mechanics'])
        ->when($request->filled('date'), fn($q) =>
            $q->whereDate('service_date', $request->date)
        )
        ->when($request->filled('month'), fn($q) =>
            $q->whereYear('service_date', substr($request->month, 0, 4))
              ->whereMonth('service_date', substr($request->month, 5, 2))
        )
        ->orderBy('service_date', 'desc')
        ->get();

    return view('reports.spareparts', compact('spareparts'));
    }

    public function mechanicReport(Request $request)
    {
        $mechanics = DB::table('service_mechanics as sm')
            ->join('mechanics as m', 'sm.mechanic_id', '=', 'm.id')
            ->join('services as s', 'sm.service_id', '=', 's.id')
            ->select('m.name as mekanik',
                DB::raw('COUNT(sm.service_id) as jumlah_service'),
                DB::raw('GROUP_CONCAT(sm.service_id) as service_ids'))
            ->when($request->filled('month'), function($q) use ($request) {
                $q->whereYear('s.service_date', substr($request->month, 0, 4))
                  ->whereMonth('s.service_date', substr($request->month, 5, 2));
            }, function($q) use ($request) {
                $q->whereDate('s.service_date', $request->date ?? now());
            })
            ->groupBy('m.id', 'm.name')
            ->get();

            dd($mechanics->toArray());

        return view('reports.mechanics', compact('mechanics'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }




}
