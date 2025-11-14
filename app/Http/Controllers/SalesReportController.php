<?php

namespace App\Http\Controllers;

use App\Models\SalesPayment;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return view('reports.sales.index');
    }
      public function getData(Request $request)
    {
        $query = SalesPayment::with(['sales.client', 'sales.items.barang'])
            ->orderBy('payment_date', 'desc');


        // Filter pencarian berdasarkan nama client / nomor sales
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sales', function ($q) use ($search) {
                $q->where('nomor_sales', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('nama_client', 'like', "%{$search}%");
                    });
            });
        }

        // Filter tanggal pembayaran
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(10);
        // dd($payments);

        // Total keseluruhan (bukan hanya halaman aktif)
        $totalAll = (clone $query)->sum('amount_paid');

        return response()->json([
            'data' => $payments,
            'total_all' => $totalAll,
        ]);
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
