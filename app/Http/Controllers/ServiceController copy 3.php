<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\ServicePayment;
use App\Models\Barang;
use App\Models\Hutang;
use App\Models\Mechanic;
use App\Models\Penjualan;
use App\Models\PenjualanJasa;
use App\Models\Product;
use App\Models\ServiceJob;
use App\Models\ServiceMechanic;
use App\Models\ServiceSparepart;
use App\Models\Transaksi;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('services.index');
    }

    public function getData(Request $request)
    {
        $query = Service::with(['vehicle.client', 'creator.pengguna', 'payments'])->orderBy('id', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('service_date', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('total_cost', 'like', "%{$search}%")
                    // cari di vehicle
                    ->orWhereHas('vehicle', function ($q2) use ($search) {
                        $q2->where('license_plate', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");
                    })
                    // cari di customer
                    ->orWhereHas('vehicle.client', function ($q3) use ($search) {
                        $q3->where('nama_client', 'like', "%{$search}%")
                            ->orWhere('no_telp', 'like', "%{$search}%")
                            ->orWhere('alamat', 'like', "%{$search}%");
                    });
            });
        }

        $services = $query->paginate(10);

        return response()->json($services);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::with('customer')->get();
        $mechanics = Mechanic::all();
        $spareparts = Product::all();

        return view('services.create', compact('vehicles', 'mechanics', 'spareparts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'   => 'required|exists:vehicles,id',
            // 'service_date' => 'required|date',
            'estimate_date' => 'nullable|date',
            'estimate_date' => 'nullable|date',
            'due_date'      => 'nullable|date',
            'id_client'      => 'nullable|string',
            'category'     => 'required|string',
            'complaint'    => 'nullable|string',
            'mechanics'    => 'required|array|min:1',
            'mechanics.*'  => 'exists:mechanics,id',
            'jobs'         => 'nullable|array',
            'jobs.*.id'    => 'required|exists:tbl_jasa,id_jasa',
            'jobs.*.price' => 'required|numeric',
            'jobs.*.qty'   => 'required|integer|min:1',
            'spareparts'         => 'nullable|array',
            'spareparts.*.id'    => 'required|exists:tbl_barang,id_barang',
            'spareparts.*.purchase_price' => 'required|numeric',
            'spareparts.*.price' => 'required|numeric',
            'spareparts.*.qty'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // hitung total
            $totalJobs = collect($request->jobs ?? [])->sum(function ($job) {
                return $job['price'] * $job['qty'];
            });

            $totalSpareparts = collect($request->spareparts ?? [])->sum(function ($sp) {
                return $sp['price'] * $sp['qty'];
            });

            $grandTotal = $totalJobs + $totalSpareparts;

            // 🔑 Convert service_date (yang datangnya cuma `YYYY-MM-DD`) jadi timestamp
            // $serviceDate = \Carbon\Carbon::parse($request->service_date)->setTimeFromTimeString(now()->format('H:i:s'));

            $transaksi = Transaksi::create([
                'tgl_transaksi' =>  now(),
                'id_pengguna'   => Auth::user()->id_pengguna,
            ]);

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

            // simpan service utama
            $service = Service::create([
                'vehicle_id'     => $request->vehicle_id,
                'service_date'   =>   now(),
                'estimate_date' =>  $request->estimate_date,
                'due_date'      =>  $request->due_date,
                'category'     => $request->category,
                'complaint'    => $request->complaint,
                'id_transaksi' => $transaksi->id_transaksi,
                'status'       => 'menunggu',
                'status_bayar' => $status_bayar,
                'total_cost'   => $grandTotal,
                'created_by'   => Auth::id(),
            ]);
            $warning = null;
            // simpan mechanics (pivot)
            foreach ($request->mechanics as $mechanicId) {
                ServiceMechanic::create([
                    'service_id'  => $service->id,
                    'mechanic_id' => $mechanicId,
                ]);
            }

            // simpan jobs
            if (!empty($request->jobs)) {
                foreach ($request->jobs as $job) {
                    ServiceJob::create([
                        'service_id' => $service->id,
                        'id_jasa'    => $job['id'],
                        'price'      => $job['price'],
                        'qty'        => $job['qty'],
                        'subtotal'   => $job['price'] * $job['qty'],
                    ]);

                    // simpan ke penjualan_jasa
                    PenjualanJasa::create([
                        'id_jasa'      => $job['id'],
                        'id_transaksi' => $transaksi->id_transaksi,
                    ]);
                }
            }

            // simpan spareparts
            if (!empty($request->spareparts)) {
                foreach ($request->spareparts as $sp) {

                    $barang = Barang::find($sp['id']);
                    if ($barang->stok_barang <= 0) {
                       return response()->json([
                            'status' => false,
                            'message' => 'Gagal menyimpan service Stok barang 0',
                            'error'   => 'stok barang 0',
                        ], 500);
                    } else {
                       ServiceSparepart::create([
                        'service_id'     => $service->id,
                        'id_barang'      => $sp['id'],
                        'price'          => $sp['price'],
                        'purchase_price' => $sp['purchase_price'],
                        'qty'            => $sp['qty'],
                        'subtotal'       => $sp['price'] * $sp['qty'],
                    ]);

                    // simpan ke penjualan
                    Penjualan::create([
                        'id_barang'     => $sp['id'],
                        'jumlah_penjualan' => $sp['qty'],
                        'harga_jual'   => $sp['price'],
                        'harga_kulak'  => $sp['purchase_price'], // Asumsikan harga kulak tidak diketahui
                        'id_transaksi' => $transaksi->id_transaksi,
                    ]);

                    $barang = Barang::find($sp['id']);
                    $barang->stok_barang -= $sp['qty'];
                    $barang->save();

                    // cek pagu
                    if ($barang->stok_barang < $barang->pagu) {
                        // TODO: trigger notifikasi stok menipis
                        $warning = "Stok {$barang->nama_barang} menipis!";
                    }
                    }


                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Service berhasil disimpan',
                'warning' => $warning ?? null
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan service',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $service = Service::with([
            'vehicle.client',   // kendaraan + pemilik
            'jobs.jasa',          // pekerjaan + detail jasa
            'spareparts.barang',  // sparepart + detail barang
            'mechanics',
            'creator.pengguna',
            'updater.pengguna',
        ])->findOrFail($id);

        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $service = Service::with([
            'vehicle.client',   // kendaraan + pemilik
            'jobs.jasa',        // pekerjaan + detail jasa
            'spareparts.barang', // sparepart + detail barang
            'mechanics',
            'creator',
            'updater',
        ])->findOrFail($id);


        return view('services.edit', compact('service'));

        // return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */

    //yanglama
    // public function update(Request $request, $id)
    // {

    //     // dd($request->all());
    //     $request->validate([
    //         'vehicle_id' => 'required|exists:vehicles,id',
    //         'service_date' => 'required|date',
    //         'category' => 'required|string|max:100',
    //         'complaint' => 'nullable|string|max:255',

    //         'mechanics' => 'required|array',
    //         'mechanics.*' => 'exists:mechanics,id',

    //         'jobs' => 'nullable|array',
    //         'jobs.*.id_jasa' => 'required|exists:tbl_jasa,id_jasa',
    //         'jobs.*.qty' => 'required|integer|min:1',
    //         'jobs.*.price' => 'required|numeric',

    //         'spareparts' => 'nullable|array',
    //         'spareparts.*.id_barang' => 'required|exists:tbl_barang,id_barang',
    //         'spareparts.*.qty' => 'required|integer|min:1',
    //         'spareparts.*.price' => 'required|numeric',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $service = Service::findOrFail($id);

    //         // Hitung total cost
    //         $totalJobs = 0;
    //         foreach ($request->jobs ?? [] as $job) {
    //             $totalJobs += $job['qty'] * $job['price'];
    //         }

    //         $totalSpareparts = 0;
    //         foreach ($request->spareparts ?? [] as $spare) {
    //             $totalSpareparts += $spare['qty'] * $spare['price'];
    //         }

    //         $grandTotal = $totalJobs + $totalSpareparts;

    //         // 🔑 Convert service_date (yang datangnya cuma `YYYY-MM-DD`) jadi timestamp
    //         $serviceDate = Carbon::parse($request->service_date)
    //             ->startOfDay() // otomatis jam 00:00:00
    //             ->format('Y-m-d H:i:s');

    //         // transaksi
    //         if (!$service->id_transaksi) {
    //             $transaksi = Transaksi::create([
    //                 'tgl_transaksi' => $serviceDate,
    //                 'id_pengguna'   => Auth::user()->id_pengguna,
    //             ]);
    //             $service->id_transaksi = $transaksi->id_transaksi;
    //         } else {
    //             $transaksi = Transaksi::find($service->id_transaksi);
    //             $transaksi->update([
    //                 'tgl_transaksi' => $serviceDate,
    //                 'id_pengguna'   => Auth::user()->id_pengguna,
    //             ]);
    //         }


    //         // Update utama
    //         $service->update([
    //             'vehicle_id'   => $request->vehicle_id,
    //             'service_date' =>  $serviceDate,
    //             'category'     => $request->category,
    //             'complaint'    => $request->complaint,
    //             'updated_by'   => Auth::id(),
    //             'total_cost'   => $grandTotal,
    //         ]);
    //         $warning = null;
    //         // Sync mekanik
    //         $service->mechanics()->sync($request->mechanics);

    //         // Hapus jobs & spareparts lama
    //         $service->jobs()->delete();
    //         // rollback stok lama dulu
    //         foreach ($service->spareparts as $oldSp) {
    //             $barang = Barang::find($oldSp->id_barang);
    //             $barang->stok_barang += $oldSp->qty; // kembalikan stok lama
    //             $barang->save();
    //         }
    //         // hapus sparepart lama + penjualan lama
    //         $service->spareparts()->delete();

    //         Penjualan::where('id_transaksi', $service->id_transaksi)->delete();
    //         PenjualanJasa::where('id_transaksi', $service->id_transaksi)->delete();


    //         // Simpan jobs baru
    //         foreach ($request->jobs ?? [] as $job) {
    //             $service->jobs()->create([
    //                 'id_jasa' => $job['id_jasa'],
    //                 'qty'     => $job['qty'],
    //                 'price'   => $job['price'],
    //                 'subtotal' => $job['qty'] * $job['price'],
    //             ]);

    //             PenjualanJasa::create([
    //                 'id_jasa'      => $job['id_jasa'],
    //                 'id_transaksi' => $service->id_transaksi,
    //             ]);
    //         }

    //         // Simpan spareparts baru
    //         foreach ($request->spareparts ?? [] as $spare) {
    //             $service->spareparts()->create([
    //                 'id_barang' => $spare['id_barang'],
    //                 'qty'       => $spare['qty'],
    //                 'price'     => $spare['price'],
    //                 'subtotal'  => $spare['qty'] * $spare['price'],
    //             ]);
    //             Penjualan::create([
    //                 'id_barang'        => $spare['id_barang'],
    //                 'jumlah_penjualan' => $spare['qty'],
    //                 'harga_jual'       => $spare['price'],
    //                 'harga_kulak'      => 0,
    //                 'id_transaksi'     => $service->id_transaksi,
    //             ]);

    //             $barang = Barang::find($spare['id_barang']);
    //             $barang->stok_barang -= $spare['qty'];
    //             $barang->save();

    //             if ($barang->stok_barang < $barang->pagu) {
    //                 // TODO: trigger notifikasi stok menipis                         $warning = "Stok {$barang->nama_barang} menipis!";
    //                 $warning = "Stok {$barang->nama_barang} menipis!";
    //             }
    //         }

    //         DB::commit();

    //         // return response()->json([
    //         //     'status' => true,
    //         //     'message' => 'Service berhasil diperbarui ' . $warning,
    //         // ]);
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Service berhasil diperbarui',
    //             'warning' => $warning, // bisa null atau string
    //         ]);
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage(),
    //         ]);
    //     }
    // }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            // 'service_date' => 'required|date',
            'estimate_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'id_client'      => 'nullable|string',
            'category' => 'required|string|max:100',
            'complaint' => 'nullable|string|max:255',

            'mechanics' => 'required|array',
            'mechanics.*' => 'exists:mechanics,id',

            'jobs' => 'nullable|array',
            'jobs.*.id_jasa' => 'required|exists:tbl_jasa,id_jasa',
            'jobs.*.qty' => 'required|integer|min:1',
            'jobs.*.price' => 'required|numeric',

            'spareparts' => 'nullable|array',
            'spareparts.*.id_barang' => 'required|exists:tbl_barang,id_barang',
            'spareparts.*.qty' => 'required|integer|min:1',
            'spareparts.*.purchase_price' => 'required|numeric',
            'spareparts.*.price' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);

            // Hitung total biaya
            $totalJobs = collect($request->jobs ?? [])->sum(fn($j) => $j['qty'] * $j['price']);
            $totalSpareparts = collect($request->spareparts ?? [])->sum(fn($s) => $s['qty'] * $s['price']);
            $grandTotal = $totalJobs + $totalSpareparts;

            // Format service_date ke timestamp
            $serviceDate = \Carbon\Carbon::parse($request->service_date)->setTimeFromTimeString(now()->format('H:i:s'));

            // Transaksi
            if (!$service->id_transaksi) {
                $transaksi = Transaksi::create([
                    'tgl_transaksi' => $serviceDate,
                    'id_pengguna'   => Auth::user()->id_pengguna,
                ]);
                $service->id_transaksi = $transaksi->id_transaksi;
            } else {
                $transaksi = Transaksi::find($service->id_transaksi);
                $transaksi->update([
                    'tgl_transaksi' => $serviceDate,
                    'id_pengguna'   => Auth::user()->id_pengguna,
                ]);
            }

            // Setelah update service
            // === Sinkronisasi Hutang ===
            if($request->due_date != $service->due_date){
            if ($request->due_date) {
                Hutang::updateOrCreate(
                    ['id_transaksi' => $service->id_transaksi],
                    [
                        'tgl_jatuh_tempo' => $request->due_date,
                        'status_piutang'  => '1',
                        'id_client'       => $request->id_client,
                    ]
                );
                $service->update(['status_bayar' => 'hutang']);
            } else {
                Hutang::where('id_transaksi', $service->id_transaksi)->delete();
                $service->update(['status_bayar' => 'belum bayar']);
            }
            }



            // Update utama
            $service->update([
                'vehicle_id'   => $request->vehicle_id,
                'service_date' =>  $serviceDate,
                'estimate_date' =>  $request->estimate_date,
                'due_date' =>  $request->due_date,
                'category'     => $request->category,
                'complaint'    => $request->complaint,
                'updated_by'   => Auth::id(),
                'total_cost'   => $grandTotal,
            ]);

            $warning = null;

            // Sync mekanik
            $service->mechanics()->sync($request->mechanics);

            /*
        |--------------------------------------------------------------------------
        | Update Jobs (Jasa)
        |--------------------------------------------------------------------------
        */
            $oldJobs = $service->jobs()->get();
            $newJobs = collect($request->jobs ?? [])->keyBy('id_jasa');

            // Hapus jasa yang sudah tidak ada di request
            foreach ($oldJobs as $oldJob) {
                if (!$newJobs->has($oldJob->id_jasa)) {
                    $oldJob->delete();
                    PenjualanJasa::where('id_transaksi', $service->id_transaksi)
                        ->where('id_jasa', $oldJob->id_jasa)
                        ->delete();
                }
            }

            // Simpan / update jasa
            foreach ($request->jobs ?? [] as $job) {
                $existing = $oldJobs->firstWhere('id_jasa', $job['id_jasa']);

                if ($existing) {
                    $existing->update([
                        'qty'      => $job['qty'],
                        'price'    => $job['price'],
                        'subtotal' => $job['qty'] * $job['price'],
                    ]);

                    PenjualanJasa::updateOrCreate(
                        [
                            'id_transaksi' => $service->id_transaksi,
                            'id_jasa'      => $job['id_jasa'],
                        ],
                        []
                    );
                } else {
                    $service->jobs()->create([
                        'id_jasa'  => $job['id_jasa'],
                        'qty'      => $job['qty'],
                        'price'    => $job['price'],
                        'subtotal' => $job['qty'] * $job['price'],
                    ]);

                    PenjualanJasa::create([
                        'id_transaksi' => $service->id_transaksi,
                        'id_jasa'      => $job['id_jasa'],
                    ]);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Update Spareparts
        |--------------------------------------------------------------------------
        */
            $oldSpareparts = $service->spareparts()->get();
            $newSpareparts = collect($request->spareparts ?? [])->keyBy('id_barang');

            // Hapus sparepart yang sudah tidak ada di request (rollback stok)
            foreach ($oldSpareparts as $oldSp) {
                if (!$newSpareparts->has($oldSp->id_barang)) {
                    $barang = Barang::find($oldSp->id_barang);
                    if ($barang) {
                        $barang->stok_barang += $oldSp->qty;
                        $barang->save();
                    }

                    $oldSp->delete();

                    Penjualan::where('id_transaksi', $service->id_transaksi)
                        ->where('id_barang', $oldSp->id_barang)
                        ->delete();
                }
            }

            // Simpan / update spareparts
            foreach ($request->spareparts ?? [] as $spare) {
                $existing = $oldSpareparts->firstWhere('id_barang', $spare['id_barang']);

                if ($existing) {
                    $diffQty = $spare['qty'] - $existing->qty;

                    $existing->update([
                        'qty'            => $spare['qty'],
                        'price'          => $spare['price'],
                        'purchase_price' => $spare['purchase_price'],
                        'subtotal'       => $spare['qty'] * $spare['price'],
                    ]);

                    Penjualan::updateOrCreate(
                        [
                            'id_transaksi' => $service->id_transaksi,
                            'id_barang'    => $spare['id_barang'],
                        ],
                        [
                            'jumlah_penjualan' => $spare['qty'],
                            'harga_jual'       => $spare['price'],
                            'harga_kulak'       => $spare['purchase_price'],
                        ]
                    );

                    $barang = Barang::find($spare['id_barang']);
                    if ($barang) {
                        $barang->stok_barang -= $diffQty;
                        $barang->save();

                        if ($barang->stok_barang < $barang->pagu) {
                            $warning = "Stok {$barang->nama_barang} menipis!";
                        }
                    }
                } else {
                    $service->spareparts()->create([
                        'id_barang' => $spare['id_barang'],
                        'qty'       => $spare['qty'],
                        'price'     => $spare['price'],
                        'purchase_price'     => $spare['purchase_price'],
                        'subtotal'  => $spare['qty'] * $spare['price'],
                    ]);

                    Penjualan::create([
                        'id_barang'        => $spare['id_barang'],
                        'jumlah_penjualan' => $spare['qty'],
                        'harga_jual'       => $spare['price'],
                        'harga_kulak'      => $spare['purchase_price'],
                        'id_transaksi'     => $service->id_transaksi,
                    ]);

                    $barang = Barang::find($spare['id_barang']);
                    if ($barang) {
                        $barang->stok_barang -= $spare['qty'];
                        $barang->save();

                        if ($barang->stok_barang < $barang->pagu) {
                            $warning = "Stok {$barang->nama_barang} menipis!";
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Service berhasil diperbarui',
                'warning' => $warning,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage(),
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $service = Service::with('spareparts.barang')->findOrFail($id);

            // Kembalikan stok barang terlebih dahulu
            foreach ($service->spareparts as $sp) {
                $barang = $sp->barang;
                if ($barang) {
                    $barang->stok_barang += $sp->qty; // balikin stok sesuai qty pemakaian
                    $barang->save();
                }
            }

            // Hapus detail service (jobs, spareparts, mechanics)
            $service->jobs()->delete();
            $service->spareparts()->delete();
            $service->mechanics()->detach();

            // 🧾 Hapus pembayaran & hutang terkait service
            ServicePayment::where('service_id', $service->id)->delete();


            // Hapus penjualan barang & jasa terkait transaksi
            if ($service->id_transaksi) {
                Penjualan::where('id_transaksi', $service->id_transaksi)->delete();
                PenjualanJasa::where('id_transaksi', $service->id_transaksi)->delete();
                Hutang::where('id_transaksi', $service->id_transaksi)->delete();
                // Hapus transaksi utama
                Transaksi::where('id_transaksi', $service->id_transaksi)->delete();
            }

            // Hapus service
            $service->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data service beserta transaksi berhasil dihapus dan stok dikembalikan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updateStatus(Request $request, Service $service)
    {
        $request->validate([
            'status' => 'required|in:menunggu,proses,selesai,diambil'
        ]);

        $service->status = $request->status;

        // update waktu progres setiap kali status berubah
        $service->service_progress = now();

        $service->updated_by = Auth::id();

        $service->save();

        return response()->json(['message' => 'Status berhasil diubah']);
    }

    public function updateStatusbayar(Request $request, Service $service)
    {

        $request->validate([
            'status_bayar' => 'required|in:lunas,hutang'
        ]);


        $service->status_bayar = $request->status_bayar;

        $service->save();

        return response()->json(['message' => 'Status Pembayaran berhasil diubah']);
    }

    public function print($id)
    {
        $service = Service::with([
            'vehicle.client',   // kendaraan + pemilik
            'jobs.jasa',          // pekerjaan + detail jasa
            'spareparts.barang',  // sparepart + detail barang
            'mechanics',
            'creator.pengguna',
            'updater.pengguna',
        ])->findOrFail($id);

        return view('services.print', compact('service'));

        // $pdf = Pdf::loadView('services.print', compact('service'));
        // $content = $pdf->output();
        // $base64 = base64_encode($content);

        // return view('services.print-rawbt', compact('base64'));
    }



    public function paymentDetail($id)
{
    $service = Service::with([
        'jobs.jasa',
        'spareparts.barang'
    ])->findOrFail($id);

    // Hitung total jasa & sparepart
    $totalJasa = $service->jobs->sum('subtotal');
    $totalSparepart = $service->spareparts->sum('subtotal');
    $grandTotal = $totalJasa + $totalSparepart;

    return response()->json([
        'status' => true,
        'data' => [
            'service' => $service,
            'total_jasa' => $totalJasa,
            'total_sparepart' => $totalSparepart,
            'grand_total' => $grandTotal,
            'sisa_bayar' => $grandTotal - ($service->total_paid ?? 0),
        ]
    ]);
}


public function shows($id)
{
    $service = Service::with(['vehicle.client', 'jobs.jasa', 'spareparts.barang'])->findOrFail($id);
    return response()->json($service);
}

}
