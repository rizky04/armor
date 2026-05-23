<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PenjualanPlatform;
use App\Models\PenjualanPlatformItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanPlatformController extends Controller
{
    public function index()
    {
        $platformList = PenjualanPlatform::$platformList;
        return view('penjualan_platform.index', compact('platformList'));
    }

    public function getData(Request $request)
    {
        $query = PenjualanPlatform::with('creator')->latest('tanggal');

        if ($request->filled('tanggal_dari'))  $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        if ($request->filled('tanggal_sampai')) $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        if ($request->filled('platform'))       $query->where('platform', $request->platform);
        if ($request->filled('status'))         $query->where('status', $request->status);
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($s) => $s->where('nomor_penjualan', 'like', "%$q%")
                ->orWhere('nomor_pesanan', 'like', "%$q%")
                ->orWhere('nama_pembeli', 'like', "%$q%"));
        }

        $data = $query->get()->map(fn($item) => [
            'id'               => $item->id,
            'nomor_penjualan'  => $item->nomor_penjualan,
            'platform'         => $item->platform,
            'nomor_pesanan'    => $item->nomor_pesanan ?? '-',
            'nama_pembeli'     => $item->nama_pembeli ?? '-',
            'tanggal'          => $item->tanggal,
            'total_harga_jual' => $item->total_harga_jual,
            'total_modal'      => $item->total_modal,
            'penghasilan_bersih' => $item->penghasilan_bersih,
            'laba_bersih'      => $item->laba_bersih,
            'status'           => $item->status,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create()
    {
        $platformList = PenjualanPlatform::$platformList;
        return view('penjualan_platform.create', compact('platformList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'platform' => 'required|string',
        ]);

        $items = json_decode($request->items ?? '[]', true);
        if (empty($items)) {
            return response()->json(['message' => 'Minimal tambahkan 1 barang.'], 422);
        }

        DB::beginTransaction();
        try {
            $totalJual  = 0;
            $totalModal = 0;

            foreach ($items as $item) {
                $totalJual  += (float)$item['subtotal_jual'];
                $totalModal += (float)$item['subtotal_modal'];
            }

            $biayaAdmin      = (float)($request->biaya_admin ?? 0);
            $biayaPengiriman = (float)($request->biaya_pengiriman ?? 0);
            $biayaLainnya    = (float)($request->biaya_lainnya ?? 0);
            $totalBiaya      = $biayaAdmin + $biayaPengiriman + $biayaLainnya;

            $penghasilanBersih = $totalJual - $totalBiaya;
            $labaBersih        = $penghasilanBersih - $totalModal;

            $penjualan = PenjualanPlatform::create([
                'nomor_penjualan'  => PenjualanPlatform::generateNomor(),
                'platform'         => $request->platform,
                'nomor_pesanan'    => $request->nomor_pesanan,
                'nama_pembeli'     => $request->nama_pembeli,
                'tanggal'          => $request->tanggal,
                'catatan'          => $request->catatan,
                'total_harga_jual' => $totalJual,
                'total_modal'      => $totalModal,
                'biaya_admin'      => $biayaAdmin,
                'biaya_pengiriman' => $biayaPengiriman,
                'biaya_lainnya'    => $biayaLainnya,
                'penghasilan_bersih' => $penghasilanBersih,
                'laba_bersih'      => $labaBersih,
                'status'           => $request->status ?? 'selesai',
                'created_by'       => Auth::id(),
            ]);

            foreach ($items as $item) {
                PenjualanPlatformItem::create([
                    'penjualan_platform_id' => $penjualan->id,
                    'barang_id'    => $item['id_barang'],
                    'kode_barang'  => $item['kode_barang'] ?? null,
                    'nama_barang'  => $item['nama_barang'],
                    'harga_kulak'  => $item['harga_kulak'],
                    'harga_jual'   => $item['harga_jual'],
                    'qty'          => $item['qty'],
                    'subtotal_modal' => $item['subtotal_modal'],
                    'subtotal_jual'  => $item['subtotal_jual'],
                ]);

                // Potong stok barang
                if (($request->status ?? 'selesai') === 'selesai') {
                    Barang::where('id_barang', $item['id_barang'])
                          ->decrement('stok_barang', $item['qty']);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Penjualan berhasil disimpan.', 'data' => $penjualan]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $penjualan = PenjualanPlatform::with('items')->findOrFail($id);

        if ($penjualan->status === 'selesai') {
            return redirect()->route('penjualan-platform.show', $id)
                ->with('error', 'Penjualan yang sudah Selesai tidak bisa diedit.');
        }

        $platformList = PenjualanPlatform::$platformList;
        return view('penjualan_platform.edit', compact('penjualan', 'platformList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'platform' => 'required|string',
        ]);

        $items = json_decode($request->items ?? '[]', true);
        if (empty($items)) {
            return response()->json(['message' => 'Minimal tambahkan 1 barang.'], 422);
        }

        $penjualan = PenjualanPlatform::with('items')->findOrFail($id);

        if ($penjualan->status === 'selesai') {
            return response()->json(['message' => 'Penjualan yang sudah Selesai tidak bisa diedit.'], 403);
        }

        DB::beginTransaction();
        try {
            $penjualan->items()->delete();

            $totalJual  = 0;
            $totalModal = 0;

            foreach ($items as $item) {
                $totalJual  += (float)$item['subtotal_jual'];
                $totalModal += (float)$item['subtotal_modal'];
            }

            $biayaAdmin      = (float)($request->biaya_admin ?? 0);
            $biayaPengiriman = (float)($request->biaya_pengiriman ?? 0);
            $biayaLainnya    = (float)($request->biaya_lainnya ?? 0);
            $totalBiaya      = $biayaAdmin + $biayaPengiriman + $biayaLainnya;

            $penghasilanBersih = $totalJual - $totalBiaya;
            $labaBersih        = $penghasilanBersih - $totalModal;

            $newStatus = $request->status ?? 'pending';

            $penjualan->update([
                'platform'           => $request->platform,
                'nomor_pesanan'      => $request->nomor_pesanan,
                'nama_pembeli'       => $request->nama_pembeli,
                'tanggal'            => $request->tanggal,
                'catatan'            => $request->catatan,
                'total_harga_jual'   => $totalJual,
                'total_modal'        => $totalModal,
                'biaya_admin'        => $biayaAdmin,
                'biaya_pengiriman'   => $biayaPengiriman,
                'biaya_lainnya'      => $biayaLainnya,
                'penghasilan_bersih' => $penghasilanBersih,
                'laba_bersih'        => $labaBersih,
                'status'             => $newStatus,
            ]);

            foreach ($items as $item) {
                PenjualanPlatformItem::create([
                    'penjualan_platform_id' => $penjualan->id,
                    'barang_id'    => $item['id_barang'],
                    'kode_barang'  => $item['kode_barang'] ?? null,
                    'nama_barang'  => $item['nama_barang'],
                    'harga_kulak'  => $item['harga_kulak'],
                    'harga_jual'   => $item['harga_jual'],
                    'qty'          => $item['qty'],
                    'subtotal_modal' => $item['subtotal_modal'],
                    'subtotal_jual'  => $item['subtotal_jual'],
                ]);

                if ($newStatus === 'selesai') {
                    Barang::where('id_barang', $item['id_barang'])
                          ->decrement('stok_barang', $item['qty']);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Penjualan berhasil diperbarui.', 'data' => $penjualan]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $penjualan = PenjualanPlatform::with(['items', 'creator'])->findOrFail($id);
        return view('penjualan_platform.show', compact('penjualan'));
    }

    public function destroy($id)
    {
        $penjualan = PenjualanPlatform::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($penjualan->status === 'selesai') {
                foreach ($penjualan->items as $item) {
                    Barang::where('id_barang', $item->barang_id)
                          ->increment('stok_barang', $item->qty);
                }
            }
            $penjualan->delete();
            DB::commit();
            return response()->json(['message' => 'Penjualan berhasil dihapus.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function print($id)
    {
        $penjualan = PenjualanPlatform::with('items')->findOrFail($id);
        return view('penjualan_platform.print', compact('penjualan'));
    }

    public function laporan()
    {
        $platformList = PenjualanPlatform::$platformList;
        return view('penjualan_platform.laporan', compact('platformList'));
    }

    public function laporanData(Request $request)
    {
        $query = PenjualanPlatform::with('items')->where('status', 'selesai');

        if ($request->filled('tanggal_dari'))  $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        if ($request->filled('tanggal_sampai')) $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        if ($request->filled('platform'))       $query->where('platform', $request->platform);

        $data = $query->orderBy('tanggal')->get();

        return response()->json([
            'data'                => $data,
            'total_omzet'         => $data->sum('total_harga_jual'),
            'total_modal'         => $data->sum('total_modal'),
            'total_biaya_admin'   => $data->sum('biaya_admin'),
            'total_biaya_lainnya' => $data->sum('biaya_pengiriman') + $data->sum('biaya_lainnya'),
            'total_penghasilan'   => $data->sum('penghasilan_bersih'),
            'total_laba'          => $data->sum('laba_bersih'),
        ]);
    }
}
