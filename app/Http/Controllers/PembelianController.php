<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index()
    {
        return view('pembelian.index');
    }

    public function data(Request $request)
{
    $query = Pembelian::with('barang');

    if ($request->filled('search')) {
        $search = trim(preg_replace('/\s+/', ' ', $request->search));
        $keywords = explode(' ', $search);

        $query->whereHas('barang', function ($b) use ($keywords) {
            $b->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('nama_barang', 'like', "%{$word}%")
                            ->orWhere('id_barang', 'like', "%{$word}%")
                            ->orWhere('merk_barang', 'like', "%{$word}%")
                            ->orWhere('kode_barang', 'like', "%{$word}%")
                            ->orWhere('keterangan', 'like', "%{$word}%")
                            ->orWhere('jenis', 'like', "%{$word}%");
                    });
                }
            });
        });
    }

    $query = $query->orderBy('id_pembelian', 'desc')->paginate(10);

    return response()->json($query);
}


    // public function data(Request $request)
    // {
    //     $query = Pembelian::with('barang')
    //         ->when($request->search, fn($q) =>
    //             $q->whereHas('barang', fn($b) =>
    //                 $b->where('nama_barang', 'like', "%$request->search%")
    //                 ->orWhere('id_barang', 'like', "%$request->search%")
    //                 ->orwhere('merk_barang', 'like', "%$request->search%")
    //                 ->orwhere('kode_barang', 'like', "%$request->search%")
    //                 ->orWhere('keterangan', 'like', "%$request->search%")
    //                 ->orWhere('jenis', 'like', "%$request->search%")
    //             )
    //         )
    //         ->orderBy('id_pembelian', 'desc')
    //         ->paginate(10);

    //     return response()->json($query);
    // }

//     public function barang(Request $request)
// {
//     $search = $request->get('q');
//     $query = Barang::query();

//     if (!empty($search)) {
//         $query->where('id_barang', $search)
//             ->orWhere('nama_barang', 'like', "%$search%")
//             ->orWhere('merk_barang', 'like', "%$search%")
//             ->orWhere('kode_barang', 'like', "%$search%")
//             ->orWhere('keterangan', 'like', "%$search%")
//             ->orWhere('jenis', 'like', "%$search%");

//     }

//     // $data = $query->limit(20)->get();
//      $data = $query->get();

//     $formatted = $data->map(function ($item) {
//         return [
//             'id' => $item->id_barang,
//             'text' => $item->id_barang . ' - ' . $item->nama_barang . ' (' . $item->kode_barang . ')' . ' - ' . $item->merk_barang . ' (' . $item->jenis . ')' . ' - ' . $item->keterangan . ' (Stok: ' . $item->stok_barang . ')',
//             'kode_barang' => $item->kode_barang,
//             'harga_kulak' => $item->harga_kulak,
//             'harga_jual' => $item->harga_jual,
//             'stok' => $item->stok_barang,
//         ];
//     });

//     return response()->json($formatted);
// }


public function barang(Request $request)
{
    $search = trim(preg_replace('/\s+/', ' ', $request->get('q')));
    $query = Barang::query()->where('hapus', '0'); // pastikan tidak ambil barang yang dihapus

    if (!empty($search)) {
        // Pisahkan input jadi beberapa kata (contoh: "oli shell motor")
        $keywords = explode(' ', $search);

        // Untuk setiap kata, cari di semua kolom yang relevan
        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->where(function ($sub) use ($word) {
                    $sub->where('id_barang', 'like', "%{$word}%")
                        ->orWhere('kode_barang', 'like', "%{$word}%")
                        ->orWhere('nama_barang', 'like', "%{$word}%")
                        ->orWhere('merk_barang', 'like', "%{$word}%")
                        ->orWhere('keterangan', 'like', "%{$word}%")
                        ->orWhere('jenis', 'like', "%{$word}%");
                });
            }
        });
    }

    // Batasi jumlah hasil biar respons cepat
    // $data = $query->limit(30)->get();
     $data = $query->get();

    // Format data untuk Select2
    $formatted = $data->map(function ($item) {
        return [
            'id' => $item->id_barang,
            'text' => $item->id_barang . ' - ' . $item->nama_barang .
                ' (' . $item->kode_barang . ')' .
                ' - ' . $item->merk_barang .
                ' (' . $item->jenis . ')' .
                ' - ' . $item->keterangan .
                ' (Stok: ' . $item->stok_barang . ')',
            'kode_barang' => $item->kode_barang,
            'harga_kulak' => $item->harga_kulak,
            'harga_jual' => $item->harga_jual,
            'stok' => $item->stok_barang,
        ];
    });

    return response()->json($formatted);
}


    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $barang = Barang::findOrFail($request->id_barang);
            $stok_awal = $barang->stok_barang;
            $stok_akhir = $stok_awal + $request->jumlah_pembelian;

            Pembelian::create([
                'tgl_pembelian' => now(),
                'id_barang' => $request->id_barang,
                'jumlah_pembelian' => $request->jumlah_pembelian,
                'harga_kulak' => $request->harga_kulak,
                'harga_jual' => $request->harga_jual,
                'id_pengguna' => Auth::user()->id_pengguna,
            ]);

            $barang->update([
                'stok_barang' => $stok_akhir,
                'harga_kulak' => $request->harga_kulak,
                'harga_jual' => $request->harga_jual,
            ]);
        });

        return response()->json(['message' => 'Data pembelian berhasil disimpan']);
    }

    public function show($id)
    {
        $data = Pembelian::with('barang')->findOrFail($id);
        return response()->json($data);
    }

    // ✅ Tambahan fungsi edit pembelian
    public function edit($id)
    {
        $pembelian = Pembelian::with('barang')->findOrFail($id);
        return response()->json($pembelian);
    }

    // ✅ Update pembelian dan sesuaikan stok barang
    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $pembelian = Pembelian::findOrFail($id);
            $barang = Barang::findOrFail($pembelian->id_barang);

            // Hitung selisih stok berdasarkan perubahan jumlah pembelian
            $selisih = $request->jumlah_pembelian - $pembelian->jumlah_pembelian;

            // Update stok barang sesuai selisih
            $barang->stok_barang += $selisih;
            $barang->harga_kulak = $request->harga_kulak;
            $barang->harga_jual = $request->harga_jual;
            $barang->save();

            // Update data pembelian
            $pembelian->update([
                'jumlah_pembelian' => $request->jumlah_pembelian,
                'harga_kulak' => $request->harga_kulak,
                'harga_jual' => $request->harga_jual,
            ]);
        });

        return response()->json(['message' => 'Data pembelian berhasil diperbarui']);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $pembelian = Pembelian::findOrFail($id);
            $barang = Barang::findOrFail($pembelian->id_barang);

            // kurangi stok barang sesuai pembelian
            $barang->stok_barang -= $pembelian->jumlah_pembelian;
            $barang->save();

            $pembelian->delete();
        });

        return response()->json(['message' => 'Data pembelian berhasil dihapus']);
    }

    public function barangInfo($id)
    {
        $barang = Barang::findOrFail($id);
        return response()->json($barang);
    }
}
