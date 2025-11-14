<?php

namespace App\Http\Controllers;

use App\Models\SalesPayment;
use App\Models\Sales;
use App\Models\Hutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesPaymentController extends Controller
{
    /**
     * Tampilkan halaman laporan pembayaran sales
     */
    public function index()
    {
        return view('reports.sales.pembayaran-sales');
    }

    /**
     * Simpan pembayaran sales baru
     */
    public function store(Request $request, $salesId)
    {
        // dd($salesId);
        $sales = Sales::findOrFail($salesId);

        // dd($request->all() , $sales);
        $request->validate([
            'amount_paid'   => 'required|numeric|min:0',
            'change_amount' => 'required|numeric|min:0',
            'payment_type'  => 'required|string',
            'reference'     => 'nullable|string',
            'note'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            SalesPayment::create([
                'id_sales'      => $sales->id,
                'amount_paid'   => $request->amount_paid,
                'change_amount' => $request->change_amount,
                'payment_type'  => $request->payment_type,
                'reference'     => $request->reference,
                'note'          => $request->note,
                'payment_date'  => now(),
                'created_by'    => Auth::id(),
            ]);

            // Hitung total pembayaran
            $totalPaid = $sales->payments()->sum('amount_paid');

            // Ambil hutang berdasarkan transaksi
            $hutang = Hutang::where('id_transaksi', $sales->id_transaksi)->first();

            // Update status bayar dan hutang
            if ($totalPaid >= $sales->total) {
                $sales->status_bayar = 'lunas';
                if ($hutang) {
                    $hutang->status_piutang = '0'; // Lunas
                    $hutang->save();
                }
            } elseif ($totalPaid > 0 && $totalPaid < $sales->total) {
                $sales->status_bayar = 'cicil';
                if ($hutang) {
                    $hutang->status_piutang = '1'; // Masih ada sisa hutang
                    $hutang->save();
                }
            } else {
                $sales->status_bayar = 'hutang';
                if ($hutang) {
                    $hutang->status_piutang = '1'; // Belum bayar
                    $hutang->save();
                }
            }

            $sales->save();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Pembayaran berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update pembayaran sales
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount_paid'   => 'required|numeric|min:0',
            'change_amount' => 'required|numeric|min:0',
            'payment_type'  => 'required|string',
            'reference'     => 'nullable|string',
            'note'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $payment = SalesPayment::findOrFail($id);
            $payment->update([
                'amount_paid'   => $request->amount_paid,
                'change_amount' => $request->change_amount,
                'payment_type'  => $request->payment_type,
                'reference'     => $request->reference,
                'note'          => $request->note,
            ]);

            $sales = $payment->sales;
            $totalPaid = $sales->payments()->sum('amount_paid');
            $hutang = Hutang::where('id_transaksi', $sales->id_transaksi)->first();

            if ($totalPaid >= $sales->total) {
                $sales->status_bayar = 'lunas';
                if ($hutang) $hutang->status_piutang = '0';
            } elseif ($totalPaid > 0 && $totalPaid < $sales->total) {
                $sales->status_bayar = 'cicil';
                if ($hutang) $hutang->status_piutang = '1';
            } else {
                $sales->status_bayar = 'hutang';
                if ($hutang) $hutang->status_piutang = '1';
            }

            $sales->save();
            if ($hutang) $hutang->save();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Pembayaran berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Hapus pembayaran sales
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $payment = SalesPayment::findOrFail($id);
            $sales = $payment->sales;
            $payment->delete();

            $totalPaid = $sales->payments()->sum('amount_paid');
            if ($totalPaid >= $sales->total_amount) {
                $sales->status_bayar = 'lunas';
            } elseif ($totalPaid > 0) {
                $sales->status_bayar = 'cicil';
            } else {
                $sales->status_bayar = 'hutang';
            }
            $sales->save();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Pembayaran berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Ambil data semua pembayaran (untuk datatable / laporan)
     */
    public function getData(Request $request)
    {
        $query = SalesPayment::with(['sales.client'])
            ->orderBy('payment_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sales.client', function ($q) use ($search) {
                $q->where('nama_client', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(10);
        $totalAll = (clone $query)->sum('amount_paid');

        return response()->json([
            'data' => $payments,
            'total_all' => $totalAll,
        ]);
    }

    public function paymentDetail($id)
{
    // Ambil data sales beserta item
    // $sales = Sales::with(['items.barang'])->findOrFail($id);
    $sales = Sales::with(['items.barang'])->findOrFail($id);
    // dd($sales);
    $totalItems = $sales->items->sum('subtotal');
    $sisaBayar = $totalItems - ($sales->payments()->sum('amount_paid') ?? 0);

    // Hitung total harga semua item
    $totalItems = $sales->items->sum('subtotal');



    return response()->json([
        'status' => true,
        'data' => [
            'sales' => $sales,
            'items' => $sales->items,          // array barang yg dibeli
            'grand_total' => $totalItems,      // total semua item
            'sisa_bayar' => $sisaBayar, // sisa yg harus dibayar
        ]
    ]);
}

}
