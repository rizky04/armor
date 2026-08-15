<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\PenjualanPlatform;
use App\Models\TransaksiMotor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index()
    {
        $kategoriList = Pengeluaran::$kategoriList;
        return view('akuntansi.pengeluaran', compact('kategoriList'));
    }

    public function getData(Request $request)
    {
        $query = Pengeluaran::with('creator')->latest('tanggal');

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('keterangan', 'like', "%$q%")
                    ->orWhere('kategori', 'like', "%$q%");
            });
        }

        $data  = $query->get();
        $total = $data->sum('jumlah');

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'kategori'    => 'required|string',
            'keterangan'  => 'required|string|max:255',
            'jumlah'      => 'required|numeric|min:1',
        ]);

        $pengeluaran = Pengeluaran::create([
            'tanggal'    => $request->tanggal,
            'kategori'   => $request->kategori,
            'keterangan' => $request->keterangan,
            'jumlah'     => $request->jumlah,
            'catatan'    => $request->catatan,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Pengeluaran berhasil disimpan.', 'data' => $pengeluaran]);
    }

    public function destroy($id)
    {
        Pengeluaran::findOrFail($id)->delete();
        return response()->json(['message' => 'Pengeluaran berhasil dihapus.']);
    }

    public function laporan()
    {
        $kategoriList = Pengeluaran::$kategoriList;
        return view('akuntansi.laporan', compact('kategoriList'));
    }

    public function laporanData(Request $request)
    {
        $dari   = $request->tanggal_dari;
        $sampai = $request->tanggal_sampai;

        // ===== SUMBER 1: Transaksi Motor (service + barang toko) =====
        $trxQuery = TransaksiMotor::with('barangs')->where('status', 'selesai');
        if ($dari)   $trxQuery->whereDate('tanggal', '>=', $dari);
        if ($sampai) $trxQuery->whereDate('tanggal', '<=', $sampai);
        $transaksi = $trxQuery->get();

        $trxOmzetBarang = $transaksi->sum('total_barang');
        $trxOmzetJasa   = $transaksi->sum('total_jasa');
        $trxDiskon      = $transaksi->sum('diskon');
        $trxModal       = 0;
        $trxUntungBarang = 0;
        foreach ($transaksi as $trx) {
            foreach ($trx->barangs as $b) {
                $modal            = $b->harga_kulak * $b->qty;
                $trxModal        += $modal;
                $trxUntungBarang += $b->subtotal - $modal;
            }
        }
        $trxUntungJasa   = $trxOmzetJasa;
        $trxKeuntungan   = $trxUntungBarang + $trxUntungJasa - $trxDiskon;

        // ===== SUMBER 2: Penjualan Platform (Shopee, dll) =====
        $pjlQuery = PenjualanPlatform::where('status', 'selesai');
        if ($dari)   $pjlQuery->whereDate('tanggal', '>=', $dari);
        if ($sampai) $pjlQuery->whereDate('tanggal', '<=', $sampai);
        $penjualan = $pjlQuery->get();

        $pjlOmzet       = $penjualan->sum('total_harga_jual');
        $pjlModal       = $penjualan->sum('total_modal');
        $pjlBiayaAdmin  = $penjualan->sum('biaya_admin');
        $pjlBiayaKirim  = $penjualan->sum('biaya_pengiriman');
        $pjlBiayaLain   = $penjualan->sum('biaya_lainnya');
        $pjlTotalBiaya  = $pjlBiayaAdmin + $pjlBiayaKirim + $pjlBiayaLain;
        $pjlPenghasilan = $penjualan->sum('penghasilan_bersih');
        $pjlLaba        = $penjualan->sum('laba_bersih');

        // ===== TOTAL GABUNGAN =====
        $totalOmzet      = ($trxOmzetBarang + $trxOmzetJasa) + $pjlOmzet;
        $totalModal      = $trxModal + $pjlModal;
        $totalKeuntungan = $trxKeuntungan + $pjlLaba;

        // ===== PENGELUARAN =====
        $pengeluaranQuery = Pengeluaran::query();
        if ($dari)   $pengeluaranQuery->whereDate('tanggal', '>=', $dari);
        if ($sampai) $pengeluaranQuery->whereDate('tanggal', '<=', $sampai);
        $pengeluaranData  = $pengeluaranQuery->orderBy('tanggal')->get();
        $totalPengeluaran = $pengeluaranData->sum('jumlah');

        $pengeluaranPerKategori = $pengeluaranData
            ->groupBy('kategori')
            ->map(fn($g) => $g->sum('jumlah'))
            ->sortDesc();

        // ===== PEMBELIAN BARANG =====
        $pembelianQuery = Pembelian::with('barang');
        if ($dari)   $pembelianQuery->whereDate('tgl_pembelian', '>=', $dari);
        if ($sampai) $pembelianQuery->whereDate('tgl_pembelian', '<=', $sampai);
        $pembelianData  = $pembelianQuery->orderBy('tgl_pembelian')->get();
        $totalPembelian = $pembelianData->sum(fn($p) => $p->jumlah_pembelian * $p->harga_kulak);

        // ===== LABA BERSIH =====
        $labaBersih = $totalKeuntungan - $totalPengeluaran - $totalPembelian;

        // ===== 4 BAR STRUKTUR BARU =====
        // 1. Total Omzet Kotor = omzet showroom (barang+jasa) + omzet platform, sebelum potongan apapun
        $totalOmzetKotor = $totalOmzet;

        // 2. Total Potongan & Modal Barang = modal showroom + modal platform + biaya platform + diskon showroom
        $totalPotonganModal = $trxModal + $pjlModal + $pjlTotalBiaya + $trxDiskon;

        // 3. Total Pengeluaran = operasional (akuntansi) + pembelian barang (stok masuk)
        $totalPengeluaranSemua = $totalPengeluaran + $totalPembelian;

        // 4. Laba Bersih = Omzet Kotor - Potongan&Modal - Pengeluaran (identik dgn $labaBersih di atas)
        $labaBersihBaru = $totalOmzetKotor - $totalPotonganModal - $totalPengeluaranSemua;

        return response()->json([
            'transaksi_motor' => [
                'jumlah'        => $transaksi->count(),
                'omzet_barang'  => $trxOmzetBarang,
                'omzet_jasa'    => $trxOmzetJasa,
                'modal'         => $trxModal,
                'untung_barang' => $trxUntungBarang,
                'untung_jasa'   => $trxUntungJasa,
                'keuntungan'    => $trxKeuntungan,
            ],
            'penjualan_platform' => [
                'jumlah'        => $penjualan->count(),
                'omzet'         => $pjlOmzet,
                'modal'         => $pjlModal,
                'biaya_admin'   => $pjlBiayaAdmin,
                'biaya_kirim'   => $pjlBiayaKirim,
                'biaya_lain'    => $pjlBiayaLain,
                'total_biaya'   => $pjlTotalBiaya,
                'penghasilan'   => $pjlPenghasilan,
                'laba'          => $pjlLaba,
            ],
            'gabungan' => [
                'total_omzet'      => $totalOmzet,
                'total_modal'      => $totalModal,
                'total_keuntungan' => $totalKeuntungan,
            ],
            'pengeluaran' => [
                'total'        => $totalPengeluaran,
                'per_kategori' => $pengeluaranPerKategori,
                'detail'       => $pengeluaranData,
            ],
            'pembelian_barang' => [
                'total'  => $totalPembelian,
                'detail' => $pembelianData->map(fn($p) => [
                    'tgl_pembelian'   => $p->tgl_pembelian,
                    'nama_barang'     => $p->barang->nama_barang ?? '-',
                    'kode_barang'     => $p->barang->kode_barang ?? '-',
                    'jumlah'          => $p->jumlah_pembelian,
                    'harga_kulak'     => $p->harga_kulak,
                    'total'           => $p->jumlah_pembelian * $p->harga_kulak,
                ]),
            ],
            'laba_bersih' => $labaBersih,

            // ===== STRUKTUR 4 BAR (Omzet Kotor > Potongan&Modal > Pengeluaran > Laba Bersih) =====
            'omzet_kotor' => [
                'showroom' => $trxOmzetBarang + $trxOmzetJasa,
                'platform' => $pjlOmzet,
                'total'    => $totalOmzetKotor,
            ],
            'potongan_modal' => [
                'modal_showroom' => $trxModal,
                'modal_platform' => $pjlModal,
                'biaya_platform' => $pjlTotalBiaya,
                'diskon_showroom'=> $trxDiskon,
                'total'          => $totalPotonganModal,
            ],
            'pengeluaran_semua' => [
                'operasional'     => $totalPengeluaran,
                'pembelian_barang'=> $totalPembelian,
                'total'           => $totalPengeluaranSemua,
            ],
            'laba_bersih_baru' => $labaBersihBaru,
        ]);
    }
}
