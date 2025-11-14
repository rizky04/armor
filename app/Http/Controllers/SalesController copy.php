<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Http\Requests\StoreSalesRequest;
use App\Http\Requests\UpdateSalesRequest;
use App\Models\Barang;
use App\Models\SalesItem;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('sales.index');
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
        // dd($request->all());
        $request->validate([
            'id_client' => 'required|integer',
            'sales_date' => 'required|date',
            'due_date' => 'nullable|date',
            'items' => 'required|array',
            'items.*.id_barang' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Hitung total semua barang
            $total = 0;
            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['id_barang']);
                $subtotal = $barang->harga_jual * $item['qty'];
                $total += $subtotal;
            }

               $salesDate = \Carbon\Carbon::parse($request->sales_date)->setTimeFromTimeString(now()->format('H:i:s'));

            $transaksi = Transaksi::create([
                'tgl_transaksi' =>   $salesDate,
                'id_pengguna'   => Auth::user()->id_pengguna,
            ]);

            // Buat data sales utama
            $sales = Sales::create([
                'id_client' => $request->id_client,
                'id_transaksi' =>  $transaksi->id_transaksi,
                'id_user' => Auth::id(),
                'sales_date' => $salesDate,
                'due_date' => $request->due_date,
                'total' => $total,
                'note' => $request->note,
            ]);

            // Simpan item barang yang dijual
            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['id_barang']);
                $qty = $item['qty'];
                $subtotal = $barang->harga_jual * $qty;

                SalesItem::create([
                    'sales_id' => $sales->id, // relasi otomatis ke Sales
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang' => $barang->id_barang,
                    'price' => $barang->harga_jual,
                    'purchase_price' => $barang->harga_kulak,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok barang
                $barang->stok_barang -= $qty;
                $barang->save();
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
    public function show(Sales $sales)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesRequest $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sales $sales)
    {
        //
    }
}
