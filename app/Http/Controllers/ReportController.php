<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use App\Models\Service;
use App\Models\ServicePayment;
use App\Models\ServiceSparepart;
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
        // $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        // $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();
        $startDate = $request->start_date ?? Carbon::today()->toDateString();
    $endDate   = $request->end_date ?? Carbon::today()->toDateString();

        // Ambil data dengan relasi
        $services = Service::with(['jobs', 'spareparts', 'payments'])
            ->where('status_bayar', 'Lunas')
            ->betweenDates($startDate, $endDate)
            ->get();

        // Hitung total per komponen
        $totalOmzet = $services->sum(fn($s) => $s->omzet);
        $totalPendapatan = $services->sum(fn($s) => $s->total_paid);
        $totalProfit = $services->sum(fn($s) => $s->profit);

        // Pendapatan per jenis pembayaran
        $paymentsByType = ServicePayment::whereBetween(DB::raw('DATE(payment_date)'), [$startDate, $endDate])
    ->selectRaw('payment_type, SUM(amount_paid) as total')
    ->groupBy('payment_type')
    ->pluck('total', 'payment_type');

        // Siapkan default jika salah satu tidak ada
        $cashInCash = $paymentsByType['cash'] ?? 0;
        $cashInTransfer = $paymentsByType['transfer'] ?? 0;
        $cashInQris = $paymentsByType['qris'] ?? 0;


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

    // Ambil tanggal filter
    // $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
    // $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();
    $startDate = $request->start_date ?? Carbon::today()->toDateString();
    $endDate   = $request->end_date ?? Carbon::today()->toDateString();

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

// ini yang lama
// public function laporanGabungan(Request $request)
// {
//     $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
//     $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

//     // === AMBIL DATA SERVICE ===
//     $services = Service::with(['jobs.jasa', 'spareparts.barang', 'payments'])
//         ->where('status_bayar', 'Lunas')
//         ->whereBetween(DB::raw('DATE(service_date)'), [$startDate, $endDate])
//         ->get();

//     // === AMBIL DATA SALES BARANG ===
//     $sales = Sales::with(['salesItems', 'payments'])
//         ->where('status_bayar', 'Lunas')
//         ->whereBetween(DB::raw('DATE(sales_date)'), [$startDate, $endDate])
//         ->get();

//     // === HITUNG TOTAL OMZET, PENDAPATAN, PROFIT GABUNGAN ===
//     $totalOmzet = $services->sum('total_cost') + $sales->sum('total');
//     $totalPendapatan = $services->sum('total_paid') + $sales->sum('total_paid');

//         $totalProfit =
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
//                 $dayService->sum('total_cost') +
//                 $daySales->sum('total'),
//             'pendapatan' =>
//                 $dayService->sum('total_paid') +
//                 $daySales->sum('total_paid'),
//             'profit' =>
//     // Profit dari sparepart
//     $dayService->sum(fn($s) =>
//         $s->spareparts->sum(fn($p) =>
//             $p->subtotal - ($p->barang->harga_kulak * $p->qty)
//         )
//     ) +
//     // Profit dari jasa (anggap jasa tidak punya harga kulak, jadi langsung subtotal)
//     $dayService->sum(fn($s) =>
//         $s->jobs->sum(fn($j) => $j->subtotal)
//     ) +
//     // Profit dari penjualan barang
//     $daySales->sum(fn($s) =>
//         $s->salesItems->sum(fn($i) =>
//             $i->subtotal - ($i->purchase_price * $i->qty)
//         )
//     ),

//         ]);
//     }

//     // dd($daily);

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
    // Jika tidak ada input tanggal, tampilkan laporan HARI INI
    $startDate = $request->start_date ?? Carbon::today()->toDateString();
    $endDate   = $request->end_date ?? Carbon::today()->toDateString();

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
            'omzet' => $dayService->sum('total_cost') + $daySales->sum('total'),
            'pendapatan' => $dayService->sum('total_paid') + $daySales->sum('total_paid'),
            'profit' =>
                // Profit sparepart
                $dayService->sum(fn($s) =>
                    $s->spareparts->sum(fn($p) =>
                        $p->subtotal - ($p->barang->harga_kulak * $p->qty)
                    )
                ) +
                // Profit jasa
                $dayService->sum(fn($s) =>
                    $s->jobs->sum(fn($j) => $j->subtotal)
                ) +
                // Profit penjualan barang
                $daySales->sum(fn($s) =>
                    $s->salesItems->sum(fn($i) =>
                        $i->subtotal - ($i->purchase_price * $i->qty)
                    )
                ),
        ]);
    }

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



// public function mekanik(Request $request)
//     {
//        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
//         $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();
//         $mechanicIds = $request->mechanic_ids ?? [];

//         $query = Service::with([
//             'vehicle',
//             'mechanics',
//             'jobs.jasa',
//             'spareparts.barang'
//         ])->betweenDates($startDate, $endDate);

//         if (!empty($mechanicIds)) {
//             $query->whereHas('mechanics', function ($q) use ($mechanicIds) {
//                 $q->whereIn('mechanic_id', $mechanicIds);
//             });
//         }

//         $services = $query->get();

//         $mechanics = Mechanic::all();

//         return view('reports.mekanik', compact(
//             'services', 'mechanics', 'startDate', 'endDate', 'mechanicIds'
//         ));
//     }

// public function mekanik(Request $request)
// {
//     $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
//     $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();
//     $mechanicIds = $request->mechanic_ids ?? [];

//     $query = Service::with([
//         'vehicle',
//         'mechanics',
//         'jobs.jasa',
//         'spareparts.barang'
//     ])->betweenDates($startDate, $endDate);

//     if (!empty($mechanicIds)) {
//         $query->whereHas('mechanics', function ($q) use ($mechanicIds) {
//             $q->whereIn('mechanic_id', $mechanicIds);
//         });
//     }

//     $services = $query->get();

//     // Tambahkan total jasa tiap service
//     foreach ($services as $srv) {
//         $srv->total_jasa = $srv->jobs->sum('subtotal');
//     }

//     $mechanics = Mechanic::all();

//     return view('reports.mekanik', compact(
//         'services', 'mechanics', 'startDate', 'endDate', 'mechanicIds'
//     ));
// }

public function mekanik(Request $request)
{
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

    // Tambahkan total jasa per service
    foreach ($services as $srv) {
        $srv->total_jasa = $srv->jobs->sum('subtotal');
    }

    // Hitung total semua jasa (grand total)
    $grandTotal = $services->sum('total_jasa');

    $mechanics = Mechanic::all();

    return view('reports.mekanik', compact(
        'services', 'mechanics', 'startDate', 'endDate', 'mechanicIds', 'grandTotal'
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


public function viewReportProduct(){
 return view('reports.reportProduct');
}

// public function reportProduct(Request $request){
//      $start = $request->start_date;
//         $end   = $request->end_date;

//         // ==============================
//         // 1. Barang terjual dari SERVICE
//         // ==============================
//         $service = ServiceSparepart::with(['barang', 'service'])
//             ->whereHas('service', function ($q) use ($start, $end) {
//                 if ($start && $end) {
//                     $q->whereBetween('service_date', [$start, $end]);
//                 }
//             })
//             ->get()
//             ->map(function ($item) {
//                 return [
//                     'tanggal'     => $item->service->service_date,
//                     'kode_barang' => $item->barang->kode_barang,
//                     'nama_barang' => $item->barang->nama_barang,
//                     'qty'         => $item->qty,
//                     'harga_jual'  => $item->price,
//                     'harga_beli'  => $item->purchase_price,
//                     'profit'      => ($item->price - $item->purchase_price) * $item->qty,
//                     'sumber'      => 'SERVICE'
//                 ];
//             });

//         // ==============================
//         // 2. Barang terjual dari SALES
//         // ==============================
//         $sales = SalesItem::with(['barang', 'sales'])
//             ->whereHas('sales', function ($q) use ($start, $end) {
//                 if ($start && $end) {
//                     $q->whereBetween('sales_date', [$start, $end]);
//                 }
//             })
//             ->get()
//             ->map(function ($item) {
//                 return [
//                     'tanggal'     => $item->sales->sales_date,
//                     'kode_barang' => $item->barang->kode_barang,
//                     'nama_barang' => $item->barang->nama_barang,
//                     'qty'         => $item->qty,
//                     'harga_jual'  => $item->price,
//                     'harga_beli'  => $item->purchase_price,
//                     'profit'      => ($item->price - $item->purchase_price) * $item->qty,
//                     'sumber'      => 'SALES'
//                 ];
//             });

//         // Gabungkan data service + sales
//         $data = $service->merge($sales);

//         // Sort berdasar tanggal
//         $data = $data->sortBy('tanggal')->values();

//         return response()->json([
//             'success' => true,
//             'data' => $data
//         ]);
//     }



// }
public function reportProduct(Request $request)
{
    $start = $request->start_date;
    $end   = $request->end_date;
    $search = $request->search;

    // 1. SERVICE
    $serviceQuery = ServiceSparepart::with(['barang', 'service'])
        ->whereHas('service', function ($q) use ($start, $end) {
            if ($start && $end) {
                $q->whereBetween('service_date', [$start, $end]);
            }
        });

    if ($search) {
        $serviceQuery->whereHas('barang', function ($q) use ($search) {
            $q->where('nama_barang', 'like', "%$search%")
              ->orWhere('kode_barang', 'like', "%$search%");
        });
    }

    $service = $serviceQuery->get()->map(function ($item) {
        return [
            'tanggal'     => $item->service->service_date,
            'kode_barang' => $item->barang->kode_barang,
            'nama_barang' => $item->barang->nama_barang,
            'qty'         => $item->qty,
            'harga_jual'  => $item->price,
            'harga_beli'  => $item->purchase_price,
            'profit'      => ($item->price - $item->purchase_price) * $item->qty,
            'sumber'      => 'SERVICE'
        ];
    });

    // 2. SALES
    $salesQuery = SalesItem::with(['barang', 'sales'])
        ->whereHas('sales', function ($q) use ($start, $end) {
            if ($start && $end) {
                $q->whereBetween('sales_date', [$start, $end]);
            }
        });

    if ($search) {
        $salesQuery->whereHas('barang', function ($q) use ($search) {
            $q->where('nama_barang', 'like', "%$search%")
              ->orWhere('kode_barang', 'like', "%$search%");
        });
    }

    $sales = $salesQuery->get()->map(function ($item) {
        return [
            'tanggal'     => $item->sales->sales_date,
            'kode_barang' => $item->barang->kode_barang,
            'nama_barang' => $item->barang->nama_barang,
            'qty'         => $item->qty,
            'harga_jual'  => $item->price,
            'harga_beli'  => $item->purchase_price,
            'profit'      => ($item->price - $item->purchase_price) * $item->qty,
            'sumber'      => 'SALES'
        ];
    });

    $data = $service->merge($sales)->sortBy('tanggal')->values();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

public function viewReportJasa(){
 return view('reports.reportJasa');
}

public function reportJasa(Request $request)
{
    $start = $request->start_date ?? date('Y-m-01');
    $end   = $request->end_date ?? date('Y-m-t');

    // Ambil service dalam range tanggal
    $services = Service::with(['jobs.jasa'])
        ->betweenDates($start, $end)
        ->get();

    $search = trim($request->search ?? '');

    $laporan = [];

    foreach ($services as $service) {
        foreach ($service->jobs as $job) {

            // FILTER DI LEVEL JOB (PENTING)
            if ($search !== '' && stripos($job->jasa->nama_jasa ?? '', $search) === false) {
                continue; // skip kalau tidak cocok
            }

            $id_jasa = $job->id_jasa;

            if (!isset($laporan[$id_jasa])) {
                $laporan[$id_jasa] = [
                    'nama_jasa' => $job->jasa->nama_jasa ?? 'Tidak ada nama',
                    'qty' => 0,
                    'total_jual' => 0,
                    'profit' => 0,
                ];
            }

            $laporan[$id_jasa]['qty'] += $job->qty;
            $laporan[$id_jasa]['total_jual'] += $job->price * $job->qty;
            $laporan[$id_jasa]['profit'] += $job->price * $job->qty;
        }
    }

    return response()->json([
        'data' => array_values($laporan)
    ]);
}

public function reportBarangJasa(){
return view('reports.reportProductJasa');
}

public function combined(Request $request)
{
    $start = $request->start_date;
    $end   = $request->end_date;
    $search = $request->search;

    // ==============================
    // 1. Ambil laporan barang (service + sales)
    // ==============================
    $barangRes = $this->reportProduct($request)->getData();

    $barang = collect($barangRes->data);

    $totalBarangQty = $barang->sum('qty');
    $totalBarangModal = $barang->sum(fn($i) => $i->harga_beli * $i->qty);
    $totalBarangJual = $barang->sum(fn($i) => $i->harga_jual * $i->qty);
    $totalBarangProfit = $barang->sum('profit');

    // ==============================
    // 2. Ambil laporan jasa
    // ==============================
    $jasaRes = $this->reportJasa($request)->getData();

    $jasa = collect($jasaRes->data);

    $totalJasaQty = $jasa->sum('qty');
    $totalJasaOmzet = $jasa->sum('total_jual');
    $totalJasaProfit = $jasa->sum('profit');

    // ==============================
    // 3. Hitung total gabungan
    // ==============================
    $combinedOmzet = $totalBarangJual + $totalJasaOmzet;
    $combinedProfit = $totalBarangProfit + $totalJasaProfit;

    return response()->json([
        'success' => true,

        "barang" => [
            "data" => $barang,
            "total_qty" => $totalBarangQty,
            "total_jual" => $totalBarangJual,
            "total_modal" => $totalBarangModal,
            "total_profit" => $totalBarangProfit,
        ],

        "jasa" => [
            "data" => $jasa,
            "total_qty" => $totalJasaQty,
            "total_omzet" => $totalJasaOmzet,
            "total_profit" => $totalJasaProfit,
        ],

        "combined" => [
            "total_omzet" => $combinedOmzet,
            "total_profit" => $combinedProfit
        ]
    ]);
}


}
