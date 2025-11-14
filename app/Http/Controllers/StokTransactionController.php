<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\StokTransaction;
use Illuminate\Support\Facades\Auth;

class StokTransactionController extends Controller
{
    /**
     * Menampilkan halaman utama transaksi stok
     */
    public function index()
    {
        return view('stok_transaksi.index');
    }

    /**
     * Mengambil data transaksi stok (JSON, pagination, filter & search)
     */
    public function data(Request $request)
    {
        $query = StokTransaction::with('barang')->orderBy('created_at', 'desc');

        // Filter jenis transaksi
        if ($request->filled('tipe')) {
            $query->where('jenis_transaksi', $request->tipe);
        }

        // Pencarian barang
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('merk_barang', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate(10);
        return response()->json($data);
    }

    /**
     * Menyimpan transaksi stok baru
     */
    public function store(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'id_barang' => 'required|exists:tbl_barang,id_barang',
            'jenis_transaksi' => 'required|in:masuk,keluar,rusak,return_pembelian,return_penjualan,koreksi',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $barang = Barang::findOrFail($request->id_barang);

        // Stok awal sebelum transaksi
        $stok_awal = $barang->stok_barang;
        $jumlah = $request->jumlah;

        switch ($request->jenis_transaksi) {
            case 'masuk':
            case 'return_penjualan':
                $barang->stok_barang += $jumlah;
                break;

            case 'keluar':
            case 'rusak':
            case 'return_pembelian':
                if ($barang->stok_barang < $jumlah) {
                    return response()->json(['message' => 'Stok tidak mencukupi untuk transaksi ini.'], 422);
                }
                $barang->stok_barang -= $jumlah;
                break;

            case 'koreksi':
                $barang->stok_barang = $jumlah;
                break;
        }

        $barang->save();

        // Catat transaksi stok
        $transaksi = StokTransaction::create([
            'id_barang' => $barang->id_barang,
            'jenis_transaksi' => $request->jenis_transaksi,
            'jumlah' => $jumlah,
            'stok_awal' => $stok_awal,
            'stok_akhir' => $barang->stok_barang,
            'keterangan' => $request->keterangan,
            'created_by' => Auth::user()->id_pengguna ?? 'System',
        ]);

        return response()->json([
            'message' => 'Transaksi stok berhasil disimpan.',
            'data' => $transaksi,
        ]);
    }

    /**
     * Menghapus transaksi stok dan mengembalikan stok barang ke posisi awal
     */
    public function destroy($id)
    {
        $transaksi = StokTransaction::findOrFail($id);
        $barang = Barang::findOrFail($transaksi->id_barang);

        // Kembalikan stok sesuai jenis transaksi
        switch ($transaksi->jenis_transaksi) {
            case 'masuk':
            case 'return_pembelian':
                // Hapus transaksi masuk = stok dikurangi
                $barang->stok_barang -= $transaksi->jumlah;
                break;

            case 'keluar':
            case 'rusak':
            case 'return_penjualan':
                // Hapus transaksi keluar = stok dikembalikan
                $barang->stok_barang += $transaksi->jumlah;
                break;

            case 'koreksi':
                // Untuk koreksi stok, tidak dilakukan rollback otomatis
                break;
        }

        $barang->save();
        $transaksi->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus dan stok diperbarui.']);
    }
}
