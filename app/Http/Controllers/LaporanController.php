<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ambil filter dari request
        $start = $request->get('start_date');
        $end   = $request->get('end_date');
        $month = $request->get('month');

        $query = Service::with(['jobs.jasa', 'spareparts.barang', 'vehicle', 'mechanics',  ]);

        // filter range tanggal
        if ($start && $end) {
            $query->whereBetween('service_date', [$start, $end]);
        }

        // filter per bulan
        if ($month) {
            $query->whereMonth('service_date', date('m', strtotime($month)))
                  ->whereYear('service_date', date('Y', strtotime($month)));
        }

        $services = $query->get();
//  dd($services);
        // ringkasan
        $summary = [
            'total_service' => $services->count(),
            'total_cost'   => $services->sum('total_cost'),
            'total_barang'  => $services->flatMap->spareparts->sum('qty'),
        ];

        return view('reports.index', compact('services', 'summary', 'start', 'end', 'month'));
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
