<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use App\Models\Service;
use App\Models\ServiceJob;
use App\Models\ServicePayment;
use App\Models\TransaksiMotor;
use App\Models\TransaksiMotorBarang;
use App\Models\PenjualanPlatform;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bulanIni  = now()->month;
        $tahunIni  = now()->year;
        $hariIni   = now()->toDateString();

        // === TRANSAKSI MOTOR ===
        $trxHariIni    = TransaksiMotor::whereDate('tanggal', $hariIni)->where('status', 'selesai')->count();
        $trxBulanIni   = TransaksiMotor::whereYear('tanggal', $tahunIni)->whereMonth('tanggal', $bulanIni)->where('status', 'selesai')->sum('total');
        $trxDraft      = TransaksiMotor::where('status', 'draft')->count();

        // omzet & laba transaksi motor bulan ini
        $trxBarangs = TransaksiMotorBarang::whereHas('transaksi', function ($q) use ($bulanIni, $tahunIni) {
            $q->where('status', 'selesai')->whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni);
        })->get();
        $trxOmzetBarang  = $trxBarangs->sum('subtotal');
        $trxModalBarang  = $trxBarangs->sum(fn($b) => $b->harga_kulak * $b->qty);
        $trxUntungBarang = $trxOmzetBarang - $trxModalBarang;

        $trxOmzetJasa = TransaksiMotor::whereYear('tanggal', $tahunIni)->whereMonth('tanggal', $bulanIni)
            ->where('status', 'selesai')->sum('total_jasa');
        $trxKeuntungan = $trxUntungBarang + $trxOmzetJasa;

        // === PENJUALAN PLATFORM ===
        $pjlHariIni  = PenjualanPlatform::whereDate('tanggal', $hariIni)->where('status', 'selesai')->count();
        $pjlBulanIni = PenjualanPlatform::whereYear('tanggal', $tahunIni)->whereMonth('tanggal', $bulanIni)->where('status', 'selesai')->sum('total_harga_jual');
        $pjlLaba     = PenjualanPlatform::whereYear('tanggal', $tahunIni)->whereMonth('tanggal', $bulanIni)->where('status', 'selesai')->sum('laba_bersih');
        $pjlPending  = PenjualanPlatform::where('status', 'pending')->count();

        // === PENGELUARAN ===
        $pengeluaranBulanIni = Pengeluaran::whereYear('tanggal', $tahunIni)->whereMonth('tanggal', $bulanIni)->sum('jumlah');

        // === LABA BERSIH BULAN INI ===
        $labaBersihBulan = $trxKeuntungan + $pjlLaba - $pengeluaranBulanIni;

        // === GRAFIK 6 BULAN TERAKHIR ===
        $chartLabels = [];
        $chartTrx    = [];
        $chartPjl    = [];
        $chartPengeluaran = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $chartLabels[]     = $bulan->format('M Y');
            $chartTrx[]        = (float) TransaksiMotor::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)->where('status', 'selesai')->sum('total');
            $chartPjl[]        = (float) PenjualanPlatform::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)->where('status', 'selesai')->sum('penghasilan_bersih');
            $chartPengeluaran[] = (float) Pengeluaran::whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)->sum('jumlah');
        }

        // === RECENT ===
        $recentTrx = TransaksiMotor::with('creator')->latest()->take(6)->get();
        $recentPjl = PenjualanPlatform::latest()->take(6)->get();

        return view('home', compact(
            'trxHariIni', 'trxBulanIni', 'trxDraft', 'trxKeuntungan',
            'pjlHariIni', 'pjlBulanIni', 'pjlLaba', 'pjlPending',
            'pengeluaranBulanIni', 'labaBersihBulan',
            'chartLabels', 'chartTrx', 'chartPjl', 'chartPengeluaran',
            'recentTrx', 'recentPjl'
        ));
    }
}
