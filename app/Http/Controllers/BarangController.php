<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class BarangController extends Controller
{
     public function index()
    {
        return view('barang.index');
    }

    // public function getData(Request $request)
    // {
    //     $query = Barang::query();

    //     if ($request->has('search') && $request->search != '') {
    //         $query->where('id_barang', $request->search)
    //             ->orWhere('nama_barang', 'like', "%$request->search%")
    //             ->orWhere('merk_barang', 'like', "%$request->search%")
    //             ->orWhere('keterangan', 'like', "%$request->search%")
    //             ->orWhere('jenis', 'like', "%$request->search%");
    //     }

    //     // $barang = $query->where('hapus', '0')->orderBy('stok_barang', 'asc')->paginate(15);
    //     $barang = $query->where('stok_barang', '>', 'pagu')->orderBy('stok_barang', 'desc')->paginate(15);

    //     return response()->json($barang);
    // }

//     public function getData(Request $request)
// {
//     $query = Barang::query();

//     // 🔍 Filter pencarian teks
//     if ($request->has('search') && $request->search != '') {
//         $search = $request->search;
//         $query->where(function ($q) use ($search) {
//             $q->where('id_barang', $search)
//                 ->orWhere('kode_barang', 'like', "%$search%")
//                 ->orWhere('nama_barang', 'like', "%$search%")
//                 ->orWhere('merk_barang', 'like', "%$search%")
//                 ->orWhere('keterangan', 'like', "%$search%")
//                 ->orWhere('jenis', 'like', "%$search%");
//         });
//     }

//     // 🧠 Filter batas aman stok
//     if ($request->has('filter') && $request->filter != '') {
//         if ($request->filter == 'aman') {
//             // Stok aman = stok >= pagu
//             $query->whereColumn('stok_barang', '>=', 'pagu');
//         } elseif ($request->filter == 'tidak_aman') {
//             // Stok tidak aman = stok < pagu
//             $query->whereColumn('stok_barang', '<', 'pagu');
//         }
//     }

//     // 🚫 Pastikan tidak menampilkan data yang dihapus
//     $query->where('hapus', '0');

//     // 🔢 Urutkan dari stok terendah agar mudah terlihat mana yang tidak aman
//     $barang = $query->orderBy('stok_barang', 'desc')->paginate(15);

//     return response()->json($barang);
// }


//ini ori
// public function getData(Request $request)
// {
//     $query = Barang::query();

//     // 🚫 Jangan tampilkan yang sudah dihapus
//     $query->where('hapus', '0');

//     // 🔍 Pencarian multi-kata di beberapa kolom
//     if ($request->filled('search')) {
//         $search = trim(preg_replace('/\s+/', ' ', $request->search));
//         $keywords = explode(' ', $search);

//         $query->where(function ($q) use ($keywords) {
//             foreach ($keywords as $word) {
//                 $q->where(function ($sub) use ($word) {
//                     $sub->where('id_barang', 'like', "%{$word}%")
//                         ->orWhere('kode_barang', 'like', "%{$word}%")
//                         ->orWhere('nama_barang', 'like', "%{$word}%")
//                         ->orWhere('merk_barang', 'like', "%{$word}%")
//                         ->orWhere('keterangan', 'like', "%{$word}%")
//                         ->orWhere('jenis', 'like', "%{$word}%");
//                 });
//             }
//         });
//     }

//     // 🧠 Filter batas aman stok
//     if ($request->filled('filter')) {
//         if ($request->filter === 'aman') {
//             $query->whereColumn('stok_barang', '>=', 'pagu');
//         } elseif ($request->filter === 'tidak_aman') {
//             $query->whereColumn('stok_barang', '<', 'pagu');
//         }
//     }

//     // 🔢 Urutkan stok dari tertinggi (bisa diubah sesuai kebutuhan)
//     $barang = $query->orderBy('stok_barang', 'desc')->paginate(15);

//     return response()->json($barang);
// }
//ini baru

// public function getData(Request $request)
// {
//     // === AGREGASI PENJUALAN (SalesItem + ServiceSparepart) ===
//     $salesData = \App\Models\SalesItem::select(
//             'id_barang',
//             DB::raw('SUM(qty) as total_terjual'),
//             DB::raw('SUM(subtotal) as total_penjualan')
//         )
//         ->groupBy('id_barang');

//     $serviceData = \App\Models\ServiceSparepart::select(
//             'id_barang',
//             DB::raw('SUM(qty) as total_terjual'),
//             DB::raw('SUM(subtotal) as total_penjualan')
//         )
//         ->groupBy('id_barang');

//     $combined = $salesData->unionAll($serviceData);

//     $penjualan = DB::table(DB::raw("({$combined->toSql()}) as combined"))
//         ->mergeBindings($combined->getQuery())
//         ->select(
//             'id_barang',
//             DB::raw('SUM(total_terjual) as total_terjual'),
//             DB::raw('SUM(total_penjualan) as total_penjualan')
//         )
//         ->groupBy('id_barang');

//     // === QUERY UTAMA BARANG ===
//     $query = \App\Models\Barang::query()
//         ->leftJoinSub($penjualan, 'penjualan', function ($join) {
//             $join->on('tbl_barang.id_barang', '=', 'penjualan.id_barang');
//         })
//         ->where('hapus', '0')
//         ->select(
//             'tbl_barang.*',
//             DB::raw('COALESCE(penjualan.total_terjual, 0) as total_terjual'),
//             DB::raw('COALESCE(penjualan.total_penjualan, 0) as total_penjualan')
//         );

//     // === 🔍 FILTER PENCARIAN ===
//     if ($request->filled('search')) {
//         $search = trim(preg_replace('/\s+/', ' ', $request->search));
//         $keywords = explode(' ', $search);

//         $query->where(function ($q) use ($keywords) {
//             foreach ($keywords as $word) {
//                 $q->where(function ($sub) use ($word) {
//                     $sub->where('tbl_barang.id_barang', 'like', "%{$word}%")
//                         ->orWhere('kode_barang', 'like', "%{$word}%")
//                         ->orWhere('nama_barang', 'like', "%{$word}%")
//                         ->orWhere('merk_barang', 'like', "%{$word}%")
//                         ->orWhere('keterangan', 'like', "%{$word}%")
//                         ->orWhere('jenis', 'like', "%{$word}%");
//                 });
//             }
//         });
//     }

//     // === 🧠 FILTER BATAS AMAN STOK ===
//     if ($request->filled('filter')) {
//         if ($request->filter === 'aman') {
//             $query->whereColumn('stok_barang', '>=', 'pagu');
//         } elseif ($request->filter === 'tidak_aman') {
//             $query->whereColumn('stok_barang', '<', 'pagu');
//         }
//     }

//     // === 🔢 URUTAN: stok kosong/minim dulu, baru paling laku ===
//     $barang = $query
//         ->orderBy('stok_barang', 'asc')      // stok minim di atas
//         ->orderByDesc('total_terjual')       // lalu urut paling laku
//         ->paginate(15);

//     return response()->json($barang);
// }


//ini lebih baru
public function getData(Request $request)
{
    // === AGREGASI PENJUALAN ===
    $salesData = \App\Models\SalesItem::select(
        'id_barang',
        DB::raw('SUM(qty) as total_terjual'),
        DB::raw('SUM(subtotal) as total_penjualan')
    )
    ->groupBy('id_barang');

    $serviceData = \App\Models\ServiceSparepart::select(
        'id_barang',
        DB::raw('SUM(qty) as total_terjual'),
        DB::raw('SUM(subtotal) as total_penjualan')
    )
    ->groupBy('id_barang');

    $combined = $salesData->unionAll($serviceData);

    $penjualan = DB::table(DB::raw("({$combined->toSql()}) as combined"))
        ->mergeBindings($combined->getQuery())
        ->select(
            'id_barang',
            DB::raw('SUM(total_terjual) as total_terjual'),
            DB::raw('SUM(total_penjualan) as total_penjualan')
        )
        ->groupBy('id_barang');

    // === QUERY UTAMA BARANG ===
    $query = \App\Models\Barang::query()
        ->leftJoinSub($penjualan, 'penjualan', function ($join) {
            $join->on('tbl_barang.id_barang', '=', 'penjualan.id_barang');
        })
        ->where('hapus', '0')
        ->select(
            'tbl_barang.*',
            DB::raw('COALESCE(penjualan.total_terjual, 0) as total_terjual'),
            DB::raw('COALESCE(penjualan.total_penjualan, 0) as total_penjualan')
        );

    // 🔍 Pencarian multi-kolom
    if ($request->filled('search')) {
        $search = trim(preg_replace('/\s+/', ' ', $request->search));
        $keywords = explode(' ', $search);

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->where(function ($sub) use ($word) {
                    $sub->where('tbl_barang.id_barang', 'like', "%{$word}%")
                        ->orWhere('kode_barang', 'like', "%{$word}%")
                        ->orWhere('nama_barang', 'like', "%{$word}%")
                        ->orWhere('merk_barang', 'like', "%{$word}%")
                        ->orWhere('keterangan', 'like', "%{$word}%")
                        ->orWhere('jenis', 'like', "%{$word}%");
                });
            }
        });
    }

    // 🧠 Filter stok aman / tidak aman
    if ($request->filled('filter')) {
        switch ($request->filter) {
            case 'aman':
                $query->whereColumn('stok_barang', '>=', 'pagu');
                break;
            case 'tidak_aman':
                $query->whereColumn('stok_barang', '<', 'pagu');
                break;
            case 'stok_minim':
                $query->where('stok_barang', '<=', 3); // misalnya stok minim ≤ 3
                break;
            case 'terlaris':
                $query->orderByDesc('total_terjual');
                break;
            case 'tidak_terlaris':
                $query->orderBy('total_terjual', 'asc');
                break;
        }
    }

    // Default urutan
    if (!$request->filled('filter') || !in_array($request->filter, ['terlaris', 'tidak_terlaris'])) {
        $query->orderBy('stok_barang', 'asc')
              ->orderByDesc('total_terjual');
    }

    $barang = $query->paginate(15);

    return response()->json($barang);
}




    // app/Http/Controllers/SparepartController.php
    public function search(Request $request)
    {
        $search = $request->get('q');

        $spareparts = Barang::query()
            ->where('nama_barang', 'like', "%$search%")
            ->orWhere('kode_barang', 'like', "%$search%")
            ->orWhere('merk_barang', 'like', "%$search%")
            ->orWhere('hapus', '0')
            ->limit(20)
            ->get();

        $results = $spareparts->map(function ($sp) {
            return [
                'id' => $sp->id,
                'text' => $sp->nama_barang . ' (' . $sp->kode_barang . ')' . ' - ' .  $sp->merk_barang,
            ];
        });

        return response()->json(['results' => $results]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'nullable',
            'nama_barang' => 'nullable',
            'merk_barang' => 'nullable',
            'keterangan' => 'nullable',
            'lokasi' => 'nullable',
            'stok_barang' => 'nullable',
            'pagu' => 'nullable',
            'harga_kulak' => 'nullable',
            'harga_jual' => 'nullable',
            'distributor' => 'nullable',
            'jenis' => 'nullable',
            'hapus' => 'nullable',
        ]);

        Barang::create($request->all());

        return response()->json(['success' => true, 'message' => 'Product berhasil ditambahkan']);
    }
     public function show($id)
    {
        return Barang::findOrFail($id);
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'kode_barang' => 'nullable',
            'nama_barang' => 'nullable',
            'merk_barang' => 'nullable',
            'keterangan' => 'nullable',
            'lokasi' => 'nullable',
            'stok_barang' => 'nullable',
            'pagu' => 'nullable',
            'harga_kulak' => 'nullable',
            'harga_jual' => 'nullable',
            'distributor' => 'nullable',
            'jenis' => 'nullable',
            'hapus' => 'nullable',
        ]);

        $product = Barang::findOrFail($id);
        $product->update($request->all());

        return response()->json(['success' => true, 'message' => 'Product berhasil diupdate']);
    }

    public function destroy($id)
    {
        // Barang::findOrFail($id)->delete();
        // return response()->json(['success' => true, 'message' => 'Product berhasil dihapus']);
        Barang::where('id_barang', $id)->update(['hapus' => 1]);
        return response()->json(['success' => true, 'message' => 'Product berhasil dinonaktifkan']);
    }

    public function nonActive($id)
    {
        // dd($id);
        Barang::where('id_barang', $id)->update(['hapus' => 1]);
        return response()->json(['success' => true, 'message' => 'Product berhasil dinonaktifkan']);
    }



public function generateQr($id)
{
    $barang = Barang::findOrFail($id);
    // dd($barang);

    // Data yang ingin ditampilkan di QR
    // $url = url('/barang/' . $barang->id_barang);

    // Hasilkan QR Code SVG atau PNG
    // $qr = base64_encode(QrCode::format('png')->size(200)->generate($url));

    return view('barang.qr', compact('barang'));
}

public function printQr(Request $request)
{
    // dd($request->all());
    // Kalau ada pilihan barang yang mau diprint
    $ids = $request->get('ids');

    $query = Barang::query();
    if ($ids) {
        $query->whereIn('id_barang', explode(',', $ids));
    }

    $barangs = $query->where('hapus', 0)->paginate(100);

    return view('barang.print_qr', compact('barangs'));
}

public function getByCode($code)
{

    $barang = Barang::where('id_barang', $code)
                        ->where('hapus', '0')
                        ->first();
    if (!$barang) {
        return response()->json(null, 404);
    }

    return response()->json([
        'id_barang' => $barang->id_barang,
        'nama_barang' => $barang->nama_barang,
        'kode_barang' => $barang->kode_barang,
        'harga_jual' => $barang->harga_jual,
        'harga_kulak' => $barang->harga_kulak,
        'stock' => $barang->stok_barang,
        'merk' => $barang->merk_barang,
        'keterangan' => $barang->keterangan,
        'jenis' => $barang->jenis,
    ]);
}


public function getByQR($code)
{
    $barang = Barang::where('id_barang', $code)
                    ->where('hapus', '0')
                    ->first();
    if (!$barang) {
        return response()->json(['message' => 'Barang tidak ditemukan'], 404);
    }

    return response()->json([
        'id_barang'    => $barang->id_barang,
        'nama_barang'  => $barang->nama_barang,
        'kode_barang'  => $barang->kode_barang,
        'harga_jual'   => $barang->harga_jual,
        'harga_kulak'  => $barang->harga_kulak,
        'stok_barang'  => $barang->stok_barang,
        'merk_barang' => $barang->merk_barang,
        'keterangan' => $barang->keterangan,
        'jenis' => $barang->jenis,
    ]);
}



}
