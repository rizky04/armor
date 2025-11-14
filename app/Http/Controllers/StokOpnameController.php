<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\StokIpnameLog;
use App\Models\StokOpnameLog;

class StokOpnameController extends Controller
{
    public function index()
    {
        return view('stok_opname.index');
    }

    // public function data(Request $request)
    // {
    //     $query = Barang::query();

    //     // Filter berdasarkan jenis barang
    //     if ($request->filled('jenis')) {
    //         $query->where('jenis', $request->jenis);
    //     }

    //     // Pencarian berdasarkan nama, kode, atau merk
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('nama_barang', 'like', "%{$search}%")
    //             ->orWhere('id_barang', 'like', "%{$search}%")
    //             ->orWhere('keterangan', 'like', "%{$search}%")
    //             ->orWhere('jenis', 'like', "%{$search}%")
    //                 ->orWhere('kode_barang', 'like', "%{$search}%")
    //                 ->orWhere('merk_barang', 'like', "%{$search}%");
    //         });
    //     }

    //     // Pagination 10 per halaman
    //     $barang = $query->orderBy('nama_barang', 'asc')->paginate(10);

    //     return response()->json($barang);
    // }

    public function data(Request $request)
{
    $query = Barang::query()->where('hapus', '0'); // Pastikan tidak ambil data yang dihapus

    // Filter berdasarkan jenis barang (opsional)
    if ($request->filled('jenis')) {
        $query->where('jenis', $request->jenis);
    }

    // 🔍 Pencarian multi-kata di beberapa kolom
    if ($request->filled('search')) {
        $search = trim(preg_replace('/\s+/', ' ', $request->search));
        $keywords = explode(' ', $search);

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->where(function ($sub) use ($word) {
                    $sub->where('nama_barang', 'like', "%{$word}%")
                        ->orWhere('id_barang', 'like', "%{$word}%")
                        ->orWhere('kode_barang', 'like', "%{$word}%")
                        ->orWhere('merk_barang', 'like', "%{$word}%")
                        ->orWhere('keterangan', 'like', "%{$word}%")
                        ->orWhere('jenis', 'like', "%{$word}%");
                });
            }
        });
    }

    // Pagination 10 per halaman
    $barang = $query->orderBy('nama_barang', 'asc')->paginate(10);

    return response()->json($barang);
}


    public function update(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:tbl_barang,id_barang',
            'stok_fisik' => 'required|numeric|min:0',
        ]);

        $barang = Barang::findOrFail($request->id_barang);

        // Simpan stok lama (stok sistem)
        $stok_sistem = $barang->stok_barang;

        // Simpan stok fisik baru
        $stok_fisik = $request->stok_fisik;

        // Hitung selisih
        $selisih = $stok_fisik - $stok_sistem;

        // Update stok sistem di tabel barang
        $barang->stok_barang = $stok_fisik;
        $barang->save();

        // Simpan log opname
        StokOpnameLog::create([
            'id_barang' => $barang->id_barang,
            'stok_sistem' => $stok_sistem,
            'stok_fisik' => $stok_fisik,
            'selisih' => $selisih,
            'tanggal' => now(),
        ]);

        return response()->json([
            'message' => 'Stok barang berhasil diperbarui dan log opname telah disimpan.',
        ]);
    }

     public function logs()
    {
        return view('stok_opname.log');
    }

    public function logsData(Request $request)
    {
        $query = StokOpnameLog::with('barang');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('barang', function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('id_barang', 'like', "%{$search}%")
                  ->orWhere('merk_barang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('jenis', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(10);
        return response()->json($logs);
    }
}
