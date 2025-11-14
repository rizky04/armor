<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Barang;
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
        $query = Service::with(['vehicle.client'])->orderBy('id', 'desc');

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
                            ->orWhere('model', 'like', "%{$search}%");
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
            'service_date' => 'required|date',
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
            $serviceDate = Carbon::parse($request->service_date)
                ->startOfDay() // otomatis jam 00:00:00
                ->format('Y-m-d H:i:s');

            $transaksi = Transaksi::create([
                'tgl_transaksi' => $serviceDate,
                'id_pengguna'   => Auth::user()->id_pengguna,
            ]);

            // simpan service utama
            $service = Service::create([
                'vehicle_id'   => $request->vehicle_id,
                'service_date' =>  $serviceDate,
                'category'     => $request->category,
                'complaint'    => $request->complaint,
                'id_transaksi' => $transaksi->id_transaksi,
                'status'       => 'menunggu',
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
                    ServiceSparepart::create([
                        'service_id' => $service->id,
                        'id_barang'  => $sp['id'],
                        'price'      => $sp['price'],
                        'qty'        => $sp['qty'],
                        'subtotal'   => $sp['price'] * $sp['qty'],
                    ]);

                    // simpan ke penjualan
                    Penjualan::create([
                        'id_barang'     => $sp['id'],
                        'jumlah_penjualan' => $sp['qty'],
                        'harga_jual'   => $sp['price'],
                        'harga_kulak'  => 0, // Asumsikan harga kulak tidak diketahui
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
            'creator',
            'updater',
        ])->findOrFail($id);
        return response()->json($service);
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

        // $vehicles = Vehicle::with('customer')->get();
        // $mechanics = Mechanic::all();
        // $spareparts = Product::all();
        return view('services.edit', compact('service'));

        // return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, $id)
    {

        // dd($request->all());
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_date' => 'required|date',
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
            'spareparts.*.price' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);

            // Hitung total cost
            $totalJobs = 0;
            foreach ($request->jobs ?? [] as $job) {
                $totalJobs += $job['qty'] * $job['price'];
            }

            $totalSpareparts = 0;
            foreach ($request->spareparts ?? [] as $spare) {
                $totalSpareparts += $spare['qty'] * $spare['price'];
            }

            $grandTotal = $totalJobs + $totalSpareparts;

            // 🔑 Convert service_date (yang datangnya cuma `YYYY-MM-DD`) jadi timestamp
            $serviceDate = Carbon::parse($request->service_date)
                ->startOfDay() // otomatis jam 00:00:00
                ->format('Y-m-d H:i:s');

            // transaksi
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


            // Update utama
            $service->update([
                'vehicle_id'   => $request->vehicle_id,
                'service_date' =>  $serviceDate,
                'category'     => $request->category,
                'complaint'    => $request->complaint,
                'updated_by'   => Auth::id(),
                'total_cost'   => $grandTotal,
            ]);
            $warning = null;
            // Sync mekanik
            $service->mechanics()->sync($request->mechanics);

            // Hapus jobs & spareparts lama
            $service->jobs()->delete();
            // rollback stok lama dulu
            foreach ($service->spareparts as $oldSp) {
                $barang = Barang::find($oldSp->id_barang);
                $barang->stok_barang += $oldSp->qty; // kembalikan stok lama
                $barang->save();
            }
            // hapus sparepart lama + penjualan lama
            $service->spareparts()->delete();

            Penjualan::where('id_transaksi', $service->id_transaksi)->delete();
            PenjualanJasa::where('id_transaksi', $service->id_transaksi)->delete();


            // Simpan jobs baru
            foreach ($request->jobs ?? [] as $job) {
                $service->jobs()->create([
                    'id_jasa' => $job['id_jasa'],
                    'qty'     => $job['qty'],
                    'price'   => $job['price'],
                    'subtotal' => $job['qty'] * $job['price'],
                ]);

                PenjualanJasa::create([
                    'id_jasa'      => $job['id_jasa'],
                    'id_transaksi' => $service->id_transaksi,
                ]);
            }

            // Simpan spareparts baru
            foreach ($request->spareparts ?? [] as $spare) {
                $service->spareparts()->create([
                    'id_barang' => $spare['id_barang'],
                    'qty'       => $spare['qty'],
                    'price'     => $spare['price'],
                    'subtotal'  => $spare['qty'] * $spare['price'],
                ]);
                Penjualan::create([
                    'id_barang'        => $spare['id_barang'],
                    'jumlah_penjualan' => $spare['qty'],
                    'harga_jual'       => $spare['price'],
                    'harga_kulak'      => 0,
                    'id_transaksi'     => $service->id_transaksi,
                ]);

                $barang = Barang::find($spare['id_barang']);
                $barang->stok_barang -= $spare['qty'];
                $barang->save();

                if ($barang->stok_barang < $barang->pagu) {
                    // TODO: trigger notifikasi stok menipis                         $warning = "Stok {$barang->nama_barang} menipis!";
                    $warning = "Stok {$barang->nama_barang} menipis!";
                }
            }

            DB::commit();

            // return response()->json([
            //     'status' => true,
            //     'message' => 'Service berhasil diperbarui ' . $warning,
            // ]);
            return response()->json([
                'status'  => true,
                'message' => 'Service berhasil diperbarui',
                'warning' => $warning, // bisa null atau string
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }


    //     public function update(Request $request, $id)
    //     {

    //         dd($request->all());

    //     $request->validate([
    //     'vehicle_id' => 'required|exists:vehicles,id',
    //     'service_date' => 'required|date',
    //     'category' => 'required|string|max:100',
    //     'complaint' => 'nullable|string|max:255',

    //     'mechanics' => 'required|array',
    //     'mechanics.*' => 'exists:mechanics,id',

    //     'jobs' => 'nullable|array',
    //     'jobs.*.id_jasa' => 'required|exists:tbl_jasa,id_jasa', // ✅ diperbaiki
    //     'jobs.*.qty' => 'required|integer|min:1',
    //     'jobs.*.price' => 'required',
    //     'jobs.*.subtotal' => 'required',

    //     'spareparts' => 'nullable|array',
    //     'spareparts.*.id_barang' => 'required|exists:tbl_barang,id_barang',
    //     'spareparts.*.qty' => 'required|integer|min:1',
    //     'jobs.*.price' => 'required',
    //     'jobs.*.subtotal' => 'required',
    // ]);

    // // dd($data);

    //         DB::beginTransaction();
    //         try {
    //             $service = Service::findOrFail($id);

    //             // update utama
    //             $service->update([
    //                 'vehicle_id'   => $request->vehicle_id,
    //                 'service_date' => $request->service_date,
    //                 'category'     => $request->category,
    //                 'complaint'    => $request->complaint,
    //                 'updated_by'   => Auth::id(),
    //             ]);

    //             // hapus lama
    //             // hapus & simpan mekanik
    //             $service->mechanics()->sync($request->mechanics);

    //             $service->jobs()->delete();
    //             $service->spareparts()->delete();



    //             // simpan jobs
    //             foreach ($request->jobs ?? [] as $job) {
    //                 $service->jobs()->create([
    //                     'id_jasa' => $job['id_jasa'],
    //                     'qty'     => $job['qty'],
    //                     'price'   => $job['price'],
    //                     'subtotal'   => $job['qty'] * $job['price'],
    //                 ]);
    //             }

    //             // simpan spareparts
    //             foreach ($request->spareparts ?? [] as $spare) {
    //                 $service->spareparts()->create([
    //                     'id_barang' => $spare['id_barang'],
    //                     'qty'       => $spare['qty'],
    //                     'price'     => $spare['price'],
    //                     'subtotal'     => $spare['qty'] * $spare['price'],
    //                 ]);
    //             }

    //             DB::commit();
    //             return response()->json(['status' => true, 'message' => 'Service berhasil diperbarui']);
    //         } catch (\Throwable $th) {
    //             DB::rollBack();
    //             return response()->json(['status' => false, 'message' => $th->getMessage()]);
    //         }
    //     }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);

            // Hapus detail service (jobs, spareparts, mechanics)
            $service->jobs()->delete();
            $service->spareparts()->delete();
            $service->mechanics()->detach();

            // Hapus penjualan barang & jasa terkait transaksi
            if ($service->id_transaksi) {
                Penjualan::where('id_transaksi', $service->id_transaksi)->delete();
                PenjualanJasa::where('id_transaksi', $service->id_transaksi)->delete();

                // Hapus transaksi utama
                Transaksi::where('id_transaksi', $service->id_transaksi)->delete();
            }

            // Hapus service
            $service->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data service beserta transaksi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function destroy($id)
    // {
    //     try {
    //         $service = Service::findOrFail($id);

    //         // Hapus relasi jika ada (opsional, biar gak error FK)
    //         $service->jobs()->delete();
    //         $service->spareparts()->delete();
    //         $service->mechanics()->detach();

    //         $service->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data service berhasil dihapus.'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menghapus data: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function updateStatus(Request $request, Service $service)
    {
        $request->validate([
            'status' => 'required|in:menunggu,proses,selesai,diambil'
        ]);

        // $service->update(['status' => $request->status]);
        $service->status = $request->status;

        // update waktu progres setiap kali status berubah
        $service->service_progress = now();

        $service->save();

        return response()->json(['message' => 'Status berhasil diubah']);
    }
}
