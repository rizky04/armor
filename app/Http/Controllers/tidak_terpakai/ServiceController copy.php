<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Mechanic;
use App\Models\Product;
use App\Models\ServiceJob;
use App\Models\ServiceMechanic;
use App\Models\ServiceSparepart;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $query->where(function($q) use ($search) {
            $q->where('service_date', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('total_cost', 'like', "%{$search}%")
              // cari di vehicle
              ->orWhereHas('vehicle', function($q2) use ($search) {
                  $q2->where('license_plate', 'like', "%{$search}%")
                     ->orWhere('brand', 'like', "%{$search}%")
                     ->orWhere('model', 'like', "%{$search}%");
              })
              // cari di customer
              ->orWhereHas('vehicle.client', function($q3) use ($search) {
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

            // simpan service utama
            $service = Service::create([
                'vehicle_id'   => $request->vehicle_id,
                'service_date' => $request->service_date,
                'category'     => $request->category,
                'complaint'    => $request->complaint,
                'status'       => 'menunggu',
                'total_cost'   => $grandTotal,
                'created_by'   => Auth::id(),
            ]);

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
                }
            }

            DB::commit();
            return response()->json(['message' => 'Service berhasil disimpan'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan service', 'error' => $e->getMessage()], 500);
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
        $mechanics = Mechanic::all();
        $service = Service::with([
            'vehicle.client',   // kendaraan + pemilik
            'jobs.jasa',        // pekerjaan + detail jasa
            'spareparts.barang',// sparepart + detail barang
            'mechanics',
            'creator',
            'updater',
        ])->findOrFail($id);

        return view('services.edit', compact('service', 'mechanics'));
        // return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_date' => 'required|date',
            'category' => 'required|string|max:100',
            'complaint' => 'nullable|string|max:255',
            'mechanics' => 'required|array',
            'mechanics.*' => 'exists:mechanics,id',
            'jobs' => 'nullable|array',
            'jobs.*.id_jasa' => 'required|exists:tbl_jasa,id',
            'jobs.*.qty' => 'required|integer|min:1',
            'spareparts' => 'nullable|array',
            'spareparts.*.id_barang' => 'required|exists:tbl_barang,id_barang',
            'spareparts.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);

            // update utama
            $service->update([
                'vehicle_id'   => $request->vehicle_id,
                'service_date' => $request->service_date,
                'category'     => $request->category,
                'complaint'    => $request->complaint,
                'updated_by'   => Auth::id(),
            ]);

            // hapus lama
            $service->mechanics()->delete();
            $service->jobs()->delete();
            $service->spareparts()->delete();

            // simpan mekanik
            foreach ($request->mechanics as $mec) {
                $service->mechanics()->create(['mechanic_id' => $mec]);
            }

            // simpan jobs
            foreach ($request->jobs ?? [] as $job) {
                $service->jobs()->create([
                    'id_jasa' => $job['id_jasa'],
                    'qty'     => $job['qty'],
                    'harga'   => $job['harga'],
                    'total'   => $job['qty'] * $job['harga'],
                ]);
            }

            // simpan spareparts
            foreach ($request->spareparts ?? [] as $spare) {
                $service->spareparts()->create([
                    'id_barang' => $spare['id_barang'],
                    'qty'       => $spare['qty'],
                    'harga'     => $spare['harga'],
                    'total'     => $spare['qty'] * $spare['harga'],
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Service berhasil diperbarui']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);

            // Hapus relasi jika ada (opsional, biar gak error FK)
            $service->jobs()->delete();
            $service->spareparts()->delete();
            $service->mechanics()->detach();

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data service berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
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

    // $service->update(['status' => $request->status]);
    $service->status = $request->status;

    // update waktu progres setiap kali status berubah
    $service->service_progress = now();

    $service->save();

    return response()->json(['message' => 'Status berhasil diubah']);
}

}
