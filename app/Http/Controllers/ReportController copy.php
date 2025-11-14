<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use App\Models\Service;
use App\Models\ServicePayment;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{

     public function omzet(Request $request)
    {
       $month = $request->input('month') ? (int) $request->input('month') : now()->month;
$year  = $request->input('year') ? (int) $request->input('year') : now()->year;


        $transactions = Transaction::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $laporan = [
            'total_transaksi' => $transactions->count(),
            'total_omset'     => $transactions->sum('total'),
            'total_diskon'    => $transactions->sum('discount'),
            'total_pajak'     => $transactions->sum('tax'),
            'total_bersih'    => $transactions->sum('total_after_tax'),
        ];

        return view('reports.omzet', compact('laporan', 'transactions', 'month', 'year'));
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
     public function serviceReport(Request $request)
    {
        // Ambil tanggal dari filter
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        // Ambil data dengan relasi
        $services = Service::with(['jobs', 'spareparts', 'payments'])
            ->where('status_bayar', 'Lunas')
            ->betweenDates($startDate, $endDate)
            ->get();

        // Hitung total per komponen
        $totalOmzet = $services->sum(fn($s) => $s->omzet);
        $totalPendapatan = $services->sum(fn($s) => $s->total_paid);
        $totalProfit = $services->sum(fn($s) => $s->profit);


        // === Tambahan: Hitung Cash In per Jenis Pembayaran ===
        // $paymentsByType = ServicePayment::whereBetween(DB::raw('payment_date'), [$startDate, $endDate])
        //     ->selectRaw('payment_type, SUM(amount_paid) as total')
        //     ->groupBy('payment_type')
        //     ->pluck('total', 'payment_type');

        $paymentsByType = ServicePayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
    ->selectRaw('payment_type, SUM(amount_paid) as total')
    ->groupBy('payment_type')
    ->pluck('total', 'payment_type');

        // Siapkan default jika salah satu tidak ada
        $cashInCash = $paymentsByType['cash'] ?? 0;
        $cashInTransfer = $paymentsByType['transfer'] ?? 0;
        $cashInQris = $paymentsByType['qris'] ?? 0;


         // === Tambahan: Buat data harian untuk chart ===
    // $daily = collect();
    // $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
    // foreach ($period as $date) {
    //     $dayServices = $services->where('service_date', $date->toDateString());
    //     $daily->push([
    //         'date' => $date->format('Y-m-d'),
    //         'omzet' => $dayServices->sum(fn($s) => $s->omzet),
    //         'pendapatan' => $dayServices->sum(fn($s) => $s->total_paid),
    //         'profit' => $dayServices->sum(fn($s) => $s->profit),
    //     ]);
    // }

    $daily = collect();
$period = \Carbon\CarbonPeriod::create($startDate, $endDate);

foreach ($period as $date) {
    $dayServices = $services->filter(function ($service) use ($date) {
        return \Carbon\Carbon::parse($service->service_date)->isSameDay($date);
    });

    $daily->push([
        'date' => $date->format('Y-m-d'),
        'omzet' => $dayServices->sum(fn($s) => $s->omzet),
        'pendapatan' => $dayServices->sum(fn($s) => $s->total_paid),
        'profit' => $dayServices->sum(fn($s) => $s->profit),
    ]);
}

 // === Tambahan: Hitung Rata-rata Harian ===
    $average = [
        'omzet' => round($daily->avg('omzet'), 2),
        'pendapatan' => round($daily->avg('pendapatan'), 2),
        'profit' => round($daily->avg('profit'), 2),
    ];



        // Kirim ke view
        return view('reports.service', compact(
            'services',
            'totalOmzet',
            'totalPendapatan',
            'totalProfit',
            'cashInCash',
            'cashInTransfer',
            'cashInQris',
            'daily',
            'average',
            'startDate',
            'endDate'
        ));
    }

    public function laporanPenjualanBarang(Request $request)
{
    // use Carbon\Carbon;
    // use Carbon\CarbonPeriod;

    // Ambil tanggal filter
    $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
    $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

    // Ambil data Sales + relasi item & pembayaran
    $sales = Sales::with(['salesItems', 'payments'])
        ->where('status_bayar', 'lunas')
        ->whereBetween(DB::raw('DATE(sales_date)'), [$startDate, $endDate])
        ->get();

    // === Hitung total keseluruhan ===
    $totalOmzet = $sales->sum(fn($s) => $s->total);
    $totalPendapatan = $sales->sum(fn($s) => $s->total_paid);
    $totalProfit = $sales->sum(fn($s) =>
        $s->salesItems->sum(fn($i) => $i->subtotal - ($i->purchase_price * $i->qty))
    );

    // === Pendapatan per jenis pembayaran ===
    $paymentsByType = SalesPayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
        ->selectRaw('payment_type, SUM(amount_paid) as total')
        ->groupBy('payment_type')
        ->pluck('total', 'payment_type');

    $cashInCash = $paymentsByType['cash'] ?? 0;
    $cashInTransfer = $paymentsByType['transfer'] ?? 0;
    $cashInQris = $paymentsByType['qris'] ?? 0;

    // === Buat data harian untuk grafik ===
    $daily = collect();
    $period = CarbonPeriod::create($startDate, $endDate);

    foreach ($period as $date) {
        $daySales = $sales->filter(fn($s) =>
            Carbon::parse($s->sales_date)->isSameDay($date)
        );

        $daily->push([
            'date' => $date->format('Y-m-d'),
            'omzet' => $daySales->sum(fn($s) => $s->total),
            'pendapatan' => $daySales->sum(fn($s) => $s->total_paid),
            'profit' => $daySales->sum(fn($s) =>
                $s->salesItems->sum(fn($i) => $i->subtotal - ($i->purchase_price * $i->qty))
            ),
        ]);
    }

    $sales->transform(function ($sale) {
    $sale->omzet = $sale->total ?? 0;
    $sale->total_paid = $sale->payments->sum('amount_paid') ?? 0;
    $sale->profit = $sale->salesItems->sum(fn($i) => $i->subtotal - ($i->purchase_price * $i->qty));
    return $sale;
});

    // === Hitung rata-rata harian (untuk garis rata-rata chart) ===
    $average = [
        'omzet' => round($daily->avg('omzet'), 2),
        'pendapatan' => round($daily->avg('pendapatan'), 2),
        'profit' => round($daily->avg('profit'), 2),
    ];
    

    // Kirim ke view
    return view('reports.sales', compact(
        'sales',
        'totalOmzet',
        'totalPendapatan',
        'totalProfit',
        'cashInCash',
        'cashInTransfer',
        'cashInQris',
        'daily',
        'average',
        'startDate',
        'endDate'
    ));
}


// public function laporanGabungan(Request $request)
// {
    

//     $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
//     $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

//     // === AMBIL DATA SERVICE ===
//     $services = Service::with(['jobs.jasa', 'spareparts.barang', 'payments'])
//       ->where('status_bayar', 'Lunas')    
//     ->whereBetween(DB::raw('DATE(service_date)'), [$startDate, $endDate])
//         ->get();

//     // === AMBIL DATA SALES BARANG ===
//     $sales = Sales::with(['salesItems', 'payments'])
//       ->where('status_bayar', 'Lunas')   
//     ->whereBetween(DB::raw('DATE(sales_date)'), [$startDate, $endDate])
//         ->get();

//         // dd($services->sum('total_paid'));

//     // === HITUNG TOTAL OMZET, PENDAPATAN, PROFIT GABUNGAN ===
//     // $totalOmzet = $services->sum('grand_total') + $sales->sum('total');
//     $totalOmzet = $services->sum('total_cost') + $sales->sum('total');
//     $totalPendapatan = $services->sum('total_paid') + $sales->sum('total_paid');

//     $totalProfit =
//         // $services->sum(fn($s) =>
//         //     $s->spareparts->sum(fn($p) => $p->subtotal - ($p->barang->harga_kulak * $p->qty))
//         // ) 
//         $services->sum(fn($s) => $s->profit) +
//         $sales->sum(fn($s) =>
//             $s->salesItems->sum(fn($i) => $i->subtotal - ($i->purchase_price * $i->qty))
//         );

//     // === PENDAPATAN PER JENIS PEMBAYARAN ===
//     $servicePayments = ServicePayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
//         ->selectRaw('payment_type, SUM(amount_paid) as total')
//         ->groupBy('payment_type')
//         ->pluck('total', 'payment_type');

//     $salesPayments = SalesPayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
//         ->selectRaw('payment_type, SUM(amount_paid) as total')
//         ->groupBy('payment_type')
//         ->pluck('total', 'payment_type');

//     $cashInCash = ($servicePayments['cash'] ?? 0) + ($salesPayments['cash'] ?? 0);
//     $cashInTransfer = ($servicePayments['transfer'] ?? 0) + ($salesPayments['transfer'] ?? 0);
//     $cashInQris = ($servicePayments['qris'] ?? 0) + ($salesPayments['qris'] ?? 0);

//     // === GABUNG DATA HARIAN ===
//     $daily = collect();
//     $period = CarbonPeriod::create($startDate, $endDate);

//     foreach ($period as $date) {
//         $dayService = $services->filter(fn($s) => Carbon::parse($s->service_date)->isSameDay($date));
//         $daySales = $sales->filter(fn($s) => Carbon::parse($s->sales_date)->isSameDay($date));
       

//         $daily->push([
//             'date' => $date->format('Y-m-d'),
//             'omzet' =>
//                 // $dayService->sum('grand_total') +
//                 $dayService->sum('total_cost') +
//                 $daySales->sum('total'),
//             'pendapatan' =>
//                 $dayService->sum('total_paid') +
//                 $daySales->sum('total_paid'),
//             'profit' =>
//                 $dayService->sum(fn($s) =>
//                     $s->spareparts->sum(fn($p) => $p->subtotal - ($p->barang->harga_kulak * $p->qty))
//                 ) +
//                 $daySales->sum(fn($s) =>
//                     $s->salesItems->sum(fn($i) => $i->subtotal - ($i->purchase_price * $i->qty))
//                 ),
//         ]);
//     }

//     dd($daily);

//     // === RATA-RATA GABUNGAN ===
//     $average = [
//         'omzet' => round($daily->avg('omzet'), 2),
//         'pendapatan' => round($daily->avg('pendapatan'), 2),
//         'profit' => round($daily->avg('profit'), 2),
//     ];

//     // === KIRIM KE VIEW ===
//     return view('reports.laporan_gabungan', compact(
//         'totalOmzet',
//         'totalPendapatan',
//         'totalProfit',
//         'cashInCash',
//         'cashInTransfer',
//         'cashInQris',
//         'daily',
//         'average',
//         'startDate',
//         'endDate'
//     ));
// }

public function laporanGabungan(Request $request)
{
    $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
    $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

    // === AMBIL DATA SERVICE ===
    $services = Service::with(['jobs.jasa', 'spareparts.barang', 'payments'])
        ->where('status_bayar', 'Lunas')
        ->whereBetween(DB::raw('DATE(service_date)'), [$startDate, $endDate])
        ->get();

    // === AMBIL DATA SALES BARANG ===
    $sales = Sales::with(['salesItems', 'payments'])
        ->where('status_bayar', 'Lunas')
        ->whereBetween(DB::raw('DATE(sales_date)'), [$startDate, $endDate])
        ->get();

    // === HITUNG TOTAL OMZET, PENDAPATAN, PROFIT GABUNGAN ===
    $totalOmzet = $services->sum('total_cost') + $sales->sum('total');
    $totalPendapatan = $services->sum('total_paid') + $sales->sum('total_paid');

        $totalProfit =
        $services->sum(fn($s) => $s->profit) +
        $sales->sum(fn($s) =>
            $s->salesItems->sum(fn($i) => $i->subtotal - ($i->purchase_price * $i->qty))
        );

    // === PENDAPATAN PER JENIS PEMBAYARAN ===
    $servicePayments = ServicePayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
        ->selectRaw('payment_type, SUM(amount_paid) as total')
        ->groupBy('payment_type')
        ->pluck('total', 'payment_type');

    $salesPayments = SalesPayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
        ->selectRaw('payment_type, SUM(amount_paid) as total')
        ->groupBy('payment_type')
        ->pluck('total', 'payment_type');

    $cashInCash = ($servicePayments['cash'] ?? 0) + ($salesPayments['cash'] ?? 0);
    $cashInTransfer = ($servicePayments['transfer'] ?? 0) + ($salesPayments['transfer'] ?? 0);
    $cashInQris = ($servicePayments['qris'] ?? 0) + ($salesPayments['qris'] ?? 0);

    // === GABUNG DATA HARIAN ===
    $daily = collect();
    $period = CarbonPeriod::create($startDate, $endDate);

    foreach ($period as $date) {
        $dayService = $services->filter(fn($s) => Carbon::parse($s->service_date)->isSameDay($date));
        $daySales = $sales->filter(fn($s) => Carbon::parse($s->sales_date)->isSameDay($date));

        $daily->push([
            'date' => $date->format('Y-m-d'),
            'omzet' =>
                $dayService->sum('total_cost') +
                $daySales->sum('total'),
            'pendapatan' =>
                $dayService->sum('total_paid') +
                $daySales->sum('total_paid'),
            'profit' =>
    // Profit dari sparepart
    $dayService->sum(fn($s) =>
        $s->spareparts->sum(fn($p) =>
            $p->subtotal - ($p->barang->harga_kulak * $p->qty)
        )
    ) +
    // Profit dari jasa (anggap jasa tidak punya harga kulak, jadi langsung subtotal)
    $dayService->sum(fn($s) =>
        $s->jobs->sum(fn($j) => $j->subtotal)
    ) +
    // Profit dari penjualan barang
    $daySales->sum(fn($s) =>
        $s->salesItems->sum(fn($i) =>
            $i->subtotal - ($i->purchase_price * $i->qty)
        )
    ),

        ]);
    }

    // dd($daily);

    // === RATA-RATA GABUNGAN ===
    $average = [
        'omzet' => round($daily->avg('omzet'), 2),
        'pendapatan' => round($daily->avg('pendapatan'), 2),
        'profit' => round($daily->avg('profit'), 2),
    ];

    // === KIRIM KE VIEW ===
    return view('reports.laporan_gabungan', compact(
        'totalOmzet',
        'totalPendapatan',
        'totalProfit',
        'cashInCash',
        'cashInTransfer',
        'cashInQris',
        'daily',
        'average',
        'startDate',
        'endDate'
    ));
}


public function mekanik(Request $request)
    {
        // $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        // $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();
        // $mechanicIds = $request->mechanic_ids ?? [];

        // $query = Service::with([
        //     'vehicle',
        //     'mechanics',
        //     'jobs.jasa',
        //     'spareparts.barang'
        // ])->betweenDates($startDate, $endDate);

        // // Filter berdasarkan mekanik tertentu
        // if (!empty($mechanicIds)) {
        //     $query->whereHas('mechanics', function ($q) use ($mechanicIds) {
        //         $q->whereIn('mechanic_id', $mechanicIds);
        //     });
        // }

        // $services = $query->get();

        // // Kelompokkan berdasarkan mekanik
        // $laporan = [];
        // foreach ($services as $service) {
        //     foreach ($service->mechanics as $mechanic) {
        //         if (!isset($laporan[$mechanic->id])) {
        //             $laporan[$mechanic->id] = [
        //                 'mechanic' => $mechanic->name,
        //                 'services' => [],
        //             ];
        //         }
        //         $laporan[$mechanic->id]['services'][] = $service;
        //     }
        // }

        // $mechanics = Mechanic::all();

        // return view('reports.mekanik', compact('laporan', 'mechanics', 'startDate', 'endDate', 'mechanicIds'));
          $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();
        $mechanicIds = $request->mechanic_ids ?? [];

        $query = Service::with([
            'vehicle',
            'mechanics',
            'jobs.jasa',
            'spareparts.barang'
        ])->betweenDates($startDate, $endDate);

        if (!empty($mechanicIds)) {
            $query->whereHas('mechanics', function ($q) use ($mechanicIds) {
                $q->whereIn('mechanic_id', $mechanicIds);
            });
        }

        $services = $query->get();

        $mechanics = Mechanic::all();

        return view('reports.mekanik', compact(
            'services', 'mechanics', 'startDate', 'endDate', 'mechanicIds'
        ));
    }

    public function soldItemsReport(Request $request)
{
    $sales = Sales::with(['items.barang', 'client'])
        ->when($request->filled('date'), fn($q) =>
            $q->whereDate('sales_date', $request->date)
        )
        ->when($request->filled('month'), fn($q) =>
            $q->whereYear('sales_date', substr($request->month, 0, 4))
              ->whereMonth('sales_date', substr($request->month, 5, 2))
        )
        ->where('status_bayar', 'lunas')
        ->orderBy('sales_date', 'desc')
        ->get();

    return view('reports.sold-items', compact('sales'));
}




//     public function laporanPenjualanBarang(Request $request)
// {
//     // Ambil range tanggal
//     $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
//     $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

//     // === 1️⃣ Omzet (total penjualan kotor)
//     $totalOmzet = Sales::whereBetween(DB::raw('DATE(sales_date)'), [$startDate, $endDate])
//         ->sum('total');

//     // === 2️⃣ Pendapatan (cash in)
//     $totalPendapatan = SalesPayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
//         ->sum('amount_paid');

//     // === 3️⃣ Profit (penjualan - modal)
//     $totalProfit = SalesItem::whereHas('sales', function ($q) use ($startDate, $endDate) {
//             $q->whereBetween(DB::raw('DATE(sales_date)'), [$startDate, $endDate]);
//         })
//         ->select(DB::raw('SUM(subtotal - (purchase_price * qty)) as profit'))
//         ->value('profit');

//     // === 4️⃣ Pendapatan per tipe pembayaran
//     $paymentsByType = SalesPayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
//         ->selectRaw('payment_type, SUM(amount_paid) as total')
//         ->groupBy('payment_type')
//         ->pluck('total', 'payment_type');

//     $cashInCash = $paymentsByType['cash'] ?? 0;
//     $cashInTransfer = $paymentsByType['transfer'] ?? 0;
//     $cashInQris = $paymentsByType['qris'] ?? 0;

//     // === 5️⃣ Data grafik per hari
//     $chartData = Sales::select(
//             DB::raw('DATE(sales_date) as tanggal'),
//             DB::raw('SUM(total) as omzet'),
//         )
//         ->whereBetween(DB::raw('DATE(sales_date)'), [$startDate, $endDate])
//         ->groupBy('tanggal')
//         ->orderBy('tanggal')
//         ->get();

//     // Hitung pendapatan dan profit per tanggal
//     $profitData = SalesItem::select(
//             DB::raw('DATE(sales.sales_date) as tanggal'),
//             DB::raw('SUM(subtotal - (purchase_price * qty)) as profit')
//         )
//         ->join('sales', 'sales_items.sales_id', '=', 'sales.id')
//         ->whereBetween(DB::raw('DATE(sales.sales_date)'), [$startDate, $endDate])
//         ->groupBy('tanggal')
//         ->get();

//     $pendapatanData = SalesPayment::select(
//             DB::raw('DATE(payment_date) as tanggal'),
//             DB::raw('SUM(amount_paid) as pendapatan')
//         )
//         ->whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
//         ->groupBy('tanggal')
//         ->get();

//     return view('reports.sales', compact(
//         'startDate',
//         'endDate',
//         'totalOmzet',
//         'totalPendapatan',
//         'totalProfit',
//         'cashInCash',
//         'cashInTransfer',
//         'cashInQris',
//         'chartData',
//         'pendapatanData',
//         'profitData'
//     ));
// }
}
