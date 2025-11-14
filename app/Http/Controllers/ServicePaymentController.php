<?php

namespace App\Http\Controllers;

use App\Models\ServicePayment;
use App\Http\Requests\StoreServicePaymentRequest;
use App\Http\Requests\UpdateServicePaymentRequest;
use App\Models\Hutang;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reports.service.pembayaran-service');
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
   public function store(Request $request, $serviceId)
{

    // dd($request->all(), $serviceId);
    $service = Service::findOrFail($serviceId);

    $request->validate([
        'amount_paid'  => 'required|numeric|min:0',
        'change_amount'  => 'required|numeric|min:0',
        // 'payment_type' => 'required|string',
        'reference'    => 'nullable|string',
        'note'         => 'nullable|string',
    ]);

    // dd($request->all(), $service);

    DB::beginTransaction();
    try {
        ServicePayment::create([
            'service_id'   => $service->id,
            'amount_paid'  => $request->amount_paid,
            'change_amount'  => $request->change_amount,
            'payment_type' => $request->payment_type,
            'reference'    => $request->reference,
            'note'         => $request->note,
            'payment_date' => now(),
            'created_by'   => Auth::id(),
        ]);

        // Hitung total sudah dibayar
        $totalPaid = $service->payments()->sum('amount_paid');


  $hutang = Hutang::where('id_transaksi', $service->id_transaksi)->first();

        // Update status bayar service
        if ($totalPaid >= $service->total_cost) {
             $service->status_bayar = 'lunas';
            if ($hutang) {
                $hutang->status_piutang = '0';
                $hutang->save();
            }


        } elseif ($totalPaid > 0 && $totalPaid < $service->total_cost) {
             if ($hutang) {
                $hutang->status_piutang = '1'; // masih ada sisa hutang
                $hutang->save();
            }
            $service->status_bayar = 'cicil';

        } else {
             if ($hutang) {
                $hutang->status_piutang = '1'; // belum bayar sama sekali
                $hutang->save();
            }
            $service->status_bayar = 'hutang';
        }

        $service->save();

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Pembayaran berhasil disimpan']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
    }
}

  public function update(Request $request, $id)
{
    $request->validate([
        'amount_paid'  => 'required|numeric|min:0',
        'change_amount'  => 'required|numeric|min:0',
        'payment_type' => 'required|string',
        'reference'    => 'nullable|string',
        'note'         => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {
        $payment = ServicePayment::findOrFail($id);
        $payment->update([
            'amount_paid'  => $request->amount_paid,
            'change_amount'  => $request->change_amount,
            'payment_type' => $request->payment_type,
            'reference'    => $request->reference,
            'note'         => $request->note,
        ]);

        // 🔁 Recalculate total bayar
        $service = $payment->service;
        $totalPaid = $service->payments()->sum('amount_paid');

        // 🔍 Ambil data hutang jika ada
        $hutang = Hutang::where('id_transaksi', $service->id_transaksi)->first();

        // 💰 Logika status bayar & hutang



        if ($totalPaid >= $service->total_cost) {
            $service->status_bayar = 'lunas';
             if ($hutang) {
                $hutang->status_piutang = '0'; // lunas
                $hutang->save();
            }
        } elseif ($totalPaid > 0) {
            $service->status_bayar = 'cicil';
            if ($hutang) {
                $hutang->status_piutang = '1'; // masih ada sisa hutang
                $hutang->save();
            }
        } else {
            $service->status_bayar = 'hutang';
             if ($hutang) {
                $hutang->status_piutang = '1'; // belum bayar sama sekali
                $hutang->save();
            }
        }

        $service->save();

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Pembayaran berhasil diperbarui']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => $e->getMessage()]);
    }
}

 public function destroy($id)
{
    DB::beginTransaction();
    try {
        $payment = ServicePayment::findOrFail($id);
        $service = $payment->service;

        $payment->delete();

        // 🔁 Update total dan status bayar
        $totalPaid = $service->payments()->sum('amount_paid');
        if ($totalPaid >= $service->total_cost) {
            $service->status_bayar = 'lunas';
        } elseif ($totalPaid > 0) {
            $service->status_bayar = 'cicil';
        } else {
            $service->status_bayar = 'hutang';
        }
        $service->save();

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Pembayaran berhasil dihapus']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => $e->getMessage()]);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(ServicePayment $servicePayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServicePayment $servicePayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */


public function getData(Request $request)
{
    $query = ServicePayment::with(['service.vehicle.client', 'service.jobs.jasa',
        'service.spareparts.barang'])
        ->orderBy('payment_date', 'desc');

    // Filter pencarian berdasarkan nama client / nomor service
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('service', function ($q) use ($search) {
            $q->where('nomor_service', 'like', "%{$search}%")
              ->orWhereHas('vehicle.client', function ($q2) use ($search) {
                  $q2->where('nama_client', 'like', "%{$search}%");
              });
        });
    }

    // Filter tanggal (opsional)
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
    }

    $payments = $query->paginate(10);

    // Tambah total di seluruh data yang difilter (bukan cuma per halaman)
    $totalAll = (clone $query)->sum('amount_paid');

    return response()->json([
        'data' => $payments,
        'total_all' => $totalAll,
    ]);
}

 public function report()
    {
        return view('reports.service.laporan-service');
    }

  public function data(Request $request)
    {
        $query = Service::with(['vehicle.client', 'payments'])
            ->orderBy('service_date', 'desc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('service_date', [$request->start_date, $request->end_date]);
        }

        if ($request->search) {
            $query->where('nomor_service', 'like', "%{$request->search}%")
                ->orWhereHas('vehicle.client', function ($q) use ($request) {
                    $q->where('nama_client', 'like', "%{$request->search}%");
                });
        }

        $data = $query->paginate(10);

        return response()->json($data);
    }






}
