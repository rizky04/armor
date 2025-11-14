<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServicePayment;
use App\Models\ServiceSparepart;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     public function index()
    {
    //    / === TOTAL PENJUALAN BARANG ===
    $totalSales = Sales::sum('total');
    $totalPaidSales = SalesPayment::sum('amount_paid');

    // === TOTAL SERVICE ===
    $totalService = Service::sum('total_cost');
    $totalPaidService = ServicePayment::sum('amount_paid');

    // === TOTAL GABUNGAN ===
    $grandTotal = $totalSales + $totalService;
    $grandPaid = $totalPaidSales + $totalPaidService;
    $grandDue = $grandTotal - $grandPaid;

    $todaySalesCount = Sales::whereDate('sales_date', now())->count();
    $todayServiceCount = Service::whereDate('service_date', now())->count();

    // === PENJUALAN BULANAN (BARANG) ===
    $salesPerMonth = Sales::selectRaw('MONTH(sales_date) as month, SUM(total) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month')
        ->toArray();

    // === SERVICE BULANAN ===
    $servicePerMonth = Service::selectRaw('MONTH(service_date) as month, SUM(total_cost) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month')
        ->toArray();

            // ✅ TAMBAHKAN BAGIAN INI SETELAH QUERY DI ATAS
    // === ROTASI DATA BULAN SESUAI BULAN BERJALAN ===
    function rotateDataByCurrentMonth($data) {
        $currentMonth = now()->month; // Ambil bulan sekarang (1–12)
        $rotated = [];
        for ($i = 0; $i < 12; $i++) {
            $month = (($currentMonth + $i - 1) % 12) + 1; // Geser bulan
            $rotated[] = $data[$month] ?? 0; // Ambil nilai, default 0 jika kosong
        }
        return $rotated;
    }

    // Terapkan rotasi pada data
    $salesPerMonth = rotateDataByCurrentMonth($salesPerMonth);
    $servicePerMonth = rotateDataByCurrentMonth($servicePerMonth);
    // ✅ SAMPAI SINI

    // === STATUS PEMBAYARAN (GABUNG) ===
    $statusCounts = [
        'lunas' => (Sales::where('status_bayar', 'lunas')->count() + Service::where('status_bayar', 'lunas')->count()),
        'cicil' => (Sales::where('status_bayar', 'cicil')->count() + Service::where('status_bayar', 'cicil')->count()),
        'belum' => (Sales::where('status_bayar', 'belum')->count() + Service::where('status_bayar', 'belum bayar')->count()),
        'hutang' => (Sales::where('status_bayar', 'hutang')->count() + Service::where('status_bayar', 'hutang')->count()),
    ];

    // === ITEM / SERVICE TERLARIS ===
    $topItems = SalesItem::select('id_barang', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
        ->groupBy('id_barang')
        ->with('barang')
        ->orderByDesc('total_qty')
        ->take(5)
        ->get();

    $topServices = ServiceJob::select('id_jasa', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
        ->groupBy('id_jasa')
        ->with('jasa')
        ->orderByDesc('total_qty')
        ->take(5)
        ->get();

    // === DATA RECENT TRANSAKSI ===
    $recentSales = Sales::latest()->take(5)->get();
    $recentServices = Service::latest()->take(5)->get();

    return view('home', compact(
        'totalSales',
        'totalPaidSales',
        'totalService',
        'totalPaidService',
        'grandTotal',
        'grandPaid',
        'grandDue',
        'todaySalesCount',
        'todayServiceCount',
        'salesPerMonth',
        'servicePerMonth',
        'statusCounts',
        'topItems',
        'topServices',
        'recentSales',
        'recentServices'
    ));
    }
}
