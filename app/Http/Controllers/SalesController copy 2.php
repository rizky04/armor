<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Http\Requests\StoreSalesRequest;
use App\Http\Requests\UpdateSalesRequest;
use App\Models\Barang;
use App\Models\Hutang;
use App\Models\Penjualan;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return view('sales.index');
    }

    // Endpoint data untuk AJAX (DataTables / pagination manual)
    public function data(Request $request)
    {
        $query = Sales::with('client', 'items.barang' )
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('client', function ($qc) use ($request) {
                    $qc->where('nama_client', 'like', '%' . $request->search . '%');
                })->orWhere('nomor_sales', 'like', '%' . $request->search . '%');
            })
            ->orderBy('sales_date', 'desc');

        // Pagination manual (10 data per halaman)
        $sales = $query->paginate(10);

        return response()->json($sales);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    $request->validate([
        'id_client' => 'required|integer',
        // 'sales_date' => 'required|date',
        'due_date' => 'nullable|date',
        'items' => 'required|array|min:1',
        'items.*.id_barang' => 'required|integer|exists:tbl_barang,id_barang',
        'items.*.qty' => 'required|integer|min:1',
    ]);

    try {
        DB::beginTransaction();

        // Gabungkan tanggal dengan jam saat ini
        // $salesDate = \Carbon\Carbon::parse($request->sales_date)
        //     ->setTimeFromTimeString(now()->format('H:i:s'));

        // Buat transaksi utama
        $transaksi = Transaksi::create([
            'tgl_transaksi' => now(),
            'id_pengguna'   => Auth::user()->id_pengguna,
        ]);

        // Hitung total dan buat detail item
        $total = 0;

        foreach ($request->items as $item) {
            $barang = Barang::findOrFail($item['id_barang']);
            $qty = $item['qty'];

            $subtotal = $barang->harga_jual * $qty;
            $total += $subtotal;
        }


            if ($request->due_date) {
                Hutang::create([
                    'id_transaksi' =>  $transaksi->id_transaksi,
                    'tgl_jatuh_tempo' => $request->due_date,
                    'status_piutang' => '1',
                    'id_client' => $request->id_client,
                ]);
                $status_bayar = 'hutang';
            } else {
                $status_bayar = 'belum bayar';
            }

        // Buat data sales utama
        $sales = Sales::create([
            'id_client' => $request->id_client,
            'id_transaksi' => $transaksi->id_transaksi,
            'id_user' => Auth::id(),
            'sales_date' => now(),
            'due_date' => $request->due_date,
            'status_bayar' =>  $status_bayar,
            'total' => $total,
            'note' => $request->note,
        ]);



        // Simpan item penjualan
        foreach ($request->items as $item) {
            $barang = Barang::findOrFail($item['id_barang']);
            if ($barang->stok_barang <= 0) {
                 return response()->json([
            'success' => false,
            'message' => 'stok barang 0 tidak bisa melakukan transaksi',
        ], 500);
            } else {
            $qty = $item['qty'];
            $subtotal = $barang->harga_jual * $qty;

            SalesItem::create([
                'sales_id' => $sales->id,
                'id_transaksi' => $transaksi->id_transaksi,
                'id_barang' => $barang->id_barang,
                'price' => $barang->harga_jual,
                'purchase_price' => $barang->harga_kulak,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ]);

            Penjualan::create([
                'id_barang' => $barang->id_barang,
                'jumlah_penjualan' => $qty,
                'harga_jual' => $barang->harga_jual,
                'harga_kulak' => $barang->harga_kulak,
                'id_transaksi' => $transaksi->id_transaksi,
            ]); //yang ini db sistem lama orangnya

            // Kurangi stok barang
            $barang->decrement('stok_barang', $qty);
            }

        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Penjualan berhasil disimpan',
            'data' => $sales->load('client', 'salesItems.barang'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
   public function show($id)
{
    $sales = Sales::with([
        'client',
        'user',
        'items.barang',
        'payments'
    ])->findOrFail($id);

    // dd($sales);

    // Tambahkan properti helper untuk status pembayaran
    $sales->status_bayar = ($sales->payments->sum('amount_paid') >= $sales->total) ? 'lunas' : 'belum lunas';

    return view('sales.show', compact('sales'));
}


    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
{
    $sales = Sales::with('salesItems.barang', 'client')->findOrFail($id);

    return view('sales.edit', compact('sales'));
}

public function print($id)
{
    $sales = Sales::with([
        'client',
        'user',
        'items.barang',
        'payments'
    ])->findOrFail($id);

    // Total bayar dan sisa
    $totalPaid = $sales->payments->sum('amount_paid');
    $sales->total_paid = $totalPaid;
    $sales->due_amount = $sales->total - $totalPaid;

    return view('sales.print', compact('sales'));

    // $pdf = Pdf::loadView('sales.print', compact('sales'));
    // $content = $pdf->output();
    // $base64 = base64_encode($content);

    // return view('sales.print-rawbt', compact('base64'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // dd($request->all());
    $request->validate([
        'id_client' => 'required|integer',
        'sales_date' => 'required|date',
        'due_date' => 'nullable|date',
        'items' => 'required|array|min:1',
        'items.*.id_barang' => 'required|integer|exists:tbl_barang,id_barang',
        'items.*.qty' => 'required|integer|min:1',
    ]);

    try {
        DB::beginTransaction();

        $sales = Sales::findOrFail($id);
        $transaksi = Transaksi::findOrFail($sales->id_transaksi);

        // 🔁 Kembalikan stok lama
        foreach ($sales->salesItems as $oldItem) {
            $barang = Barang::find($oldItem->id_barang);
            if ($barang) {
                $barang->increment('stok_barang', $oldItem->qty);
            }
        }




        // Hapus item lama
        SalesItem::where('sales_id', $sales->id)->delete();
        Penjualan::where('id_transaksi', $sales->id_transaksi)->delete();

        // Gabungkan tanggal dengan jam saat ini
        $salesDate = \Carbon\Carbon::parse($request->sales_date)
            ->setTimeFromTimeString(now()->format('H:i:s'));

        // Hitung ulang total baru
        $total = 0;
        foreach ($request->items as $item) {
            $barang = Barang::findOrFail($item['id_barang']);
            $qty = $item['qty'];
            $subtotal = $barang->harga_jual * $qty;
            $total += $subtotal;

            // Simpan item baru
            SalesItem::create([
                'sales_id' => $sales->id,
                'id_transaksi' => $sales->id_transaksi,
                'id_barang' => $barang->id_barang,
                'price' => $barang->harga_jual,
                'purchase_price' => $barang->harga_kulak,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ]);

            Penjualan::create([
                'id_barang' => $barang->id_barang,
                'jumlah_penjualan' => $qty,
                'harga_jual' => $barang->harga_jual,
                'harga_kulak' => $barang->harga_kulak,
                'id_transaksi' => $sales->id_transaksi,
            ]);

            // Kurangi stok baru
            $barang->decrement('stok_barang', $qty);
        }

        // Update transaksi & sales
        $transaksi->update([
            'tgl_transaksi' => $salesDate,
        ]);

        // Setelah update service
            // === Sinkronisasi Hutang ===
            if ($request->due_date) {
            Hutang::updateOrCreate(
                    ['id_transaksi' => $sales->id_transaksi],
                    [
                        'tgl_jatuh_tempo' => $request->due_date,
                        'status_piutang'  => '1',
                        'id_client'       => $request->id_client,
                    ]
                );
                $sales->update(['status_bayar' => 'hutang']);
            } else {
               Hutang::where('id_transaksi', $sales->id_transaksi)->delete();
                $sales->update(['status_bayar' => 'belum bayar']);
            }



        $sales->update([
            'id_client' => $request->id_client,
            'sales_date' => $salesDate,
            'due_date' => $request->due_date,
            'total' => $total,
            'note' => $request->note,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data penjualan berhasil diperbarui',
            'data' => $sales->load('client', 'salesItems.barang'),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
  public function destroy($id)
{
    try {
        DB::beginTransaction();

        $sales = Sales::with('salesItems')->findOrFail($id);
        $transaksi = Transaksi::find($sales->id_transaksi);

        // ✅ Kembalikan stok barang
        foreach ($sales->salesItems as $item) {
            $barang = Barang::find($item->id_barang);
            if ($barang) {
                $barang->increment('stok_barang', $item->qty);
            }
        }

        // ✅ Hapus data terkait
        SalesItem::where('sales_id', $sales->id)->delete();
        SalesPayment::where('id_sales', $sales->id)->delete();
        Penjualan::where('id_transaksi', $sales->id_transaksi)->delete();
        Hutang::where('id_transaksi', $sales->id_transaksi)->delete();
        // ✅ Hapus transaksi utama dan data sales
        if ($transaksi) {
            $transaksi->delete();
        }

        $sales->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data penjualan berhasil dihapus.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus data: ' . $e->getMessage(),
        ], 500);
    }
}

}
