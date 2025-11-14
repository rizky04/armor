<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Mechanic;
use App\Models\Product;
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
    $query = Service::with(['vehicle.customer'])->orderBy('id', 'desc');

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
              ->orWhereHas('vehicle.customer', function($q3) use ($search) {
                  $q3->where('name', 'like', "%{$search}%")
                     ->orWhere('phone', 'like', "%{$search}%")
                     ->orWhere('address', 'like', "%{$search}%");
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
    dd($request->all());
    // 1. Validasi Data
    $request->validate([
        'vehicle_id'   => 'required|exists:vehicles,id',
        'service_date' => 'required|date',
        // Pastikan 'fast service' atau 'inap' sesuai enum di migrasi
        'category'     => 'required|in:fast service,inap',
        'complaint'    => 'nullable|string',
        'mechanics'    => 'required|array',
        'mechanics.*'  => 'exists:mechanics,id',
        'jobs'         => 'nullable|array',
        'jobs.*.job_name'  => 'nullable|string|max:255',
        'jobs.*.job_price' => 'nullable|numeric|min:0', // Validasi harga sebagai numerik
        'spareparts'   => 'nullable|array',
        'spareparts.*.sparepart_id' => 'required|exists:products,id',
        'spareparts.*.quantity'     => 'required|integer|min:1',
    ]);

    DB::beginTransaction();
    try {
        // 2. Simpan Service Utama
        $service = Service::create([
            'vehicle_id'   => $request->vehicle_id,
            'service_date' => $request->service_date,
            'status'       => 'menunggu', // Sesuai default di skema migrasi Anda
            'category'     => $request->category,
            'complaint'    => $request->complaint,
            'total_cost'   => 0, // Dihitung di bawah
            'created_by'   => Auth::id(),
            'updated_by'   => Auth::id(),
        ]);


        // 3. Attach Mechanics
        $service->mechanics()->sync($request->mechanics);

        $total_cost = 0;

        // 4. Simpan Service Jobs dan Hitung Total
        if ($request->has('jobs') && is_array($request->jobs)) {
            foreach ($request->jobs as $job) {
                if (!empty($job['job_name'])) {
                    // Pastikan harga dikonversi ke numerik
                    $jobPrice = (float) ($job['job_price'] ?? 0);

                    $service->jobs()->create([
                        'job_name'  => $job['job_name'],
                        'job_price' => $jobPrice,
                    ]);
                    $total_cost += $jobPrice;
                }
            }
        }

        // // 5. Simpan Service Spareparts dan Hitung Total
        if ($request->has('spareparts') && is_array($request->spareparts)) {
            // Ambil harga sparepart dari master table untuk perhitungan
            $sparepartIds = collect($request->spareparts)->pluck('sparepart_id')->filter()->unique();
            // $sparepartPrices = Product::whereIn('id', $sparepartIds)->pluck('price', 'id');
            $sparepartPrices = Product::whereIn('id', $sparepartIds)->pluck('id');

            foreach ($request->spareparts as $sp) {
                if (!empty($sp['sparepart_id'])) {
                    $sparepartId = (int) $sp['sparepart_id'];
                    $quantity = (int) ($sp['quantity'] ?? 1);
                    // $price = (float) ($sparepartPrices[$sparepartId] ?? 0); // Ambil harga dari master

                    // $subtotal = $price * $quantity;

                    $service->spareparts()->create([
                        'product_id' => $sparepartId,
                        'quantity'     => $quantity,
                        // 'subtotal'     => $subtotal, // Simpan subtotal ke tabel pivot service_sparepart
                    ]);

                    // $total_cost += $subtotal;
                }
            }
        }

        // // 6. Update Total Cost
        // // $service->update(['total_cost' => $total_cost]);

        DB::commit();

        return redirect()->route('services.index')->with('success', 'Service berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        // Log error untuk debugging lebih lanjut
        Log::error('Gagal menyimpan service: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Gagal menyimpan data service. Detail: ' . $e->getMessage()])->withInput();
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::with([
            'vehicle.customer',
            'mechanics',
            'jobs',
            'spareparts.sparepart',
            'creator',
            'updater'
        ])->findOrFail($id);


        return response()->json($service);
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
{
    $service = Service::with([
        'spareparts.sparepart',
        'jobs',
        'mechanics'
    ])->findOrFail($id);

    return response()->json($service);
}

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    // 1. Validasi Data
    $request->validate([
        'vehicle_id'   => 'required|exists:vehicles,id',
        'service_date' => 'required|date',
        'category'     => 'required|in:fast service,inap',
        'complaint'    => 'nullable|string',
        'status'        => 'nullable|string',
        'mechanics'    => 'required|array',
        'mechanics.*'  => 'exists:mechanics,id',
        'jobs'         => 'nullable|array',
        'jobs.*.job_name'  => 'nullable|string|max:255',
        'jobs.*.job_price' => 'nullable|numeric|min:0',
        'spareparts'   => 'nullable|array',
        'spareparts.*.sparepart_id' => 'required|exists:products,id',
        'spareparts.*.quantity'     => 'required|integer|min:1',
    ]);

    DB::beginTransaction();
    try {
        // 2. Ambil service lama
        $service = Service::findOrFail($id);

        // 3. Update data utama
        $service->update([
            'vehicle_id'   => $request->vehicle_id,
            'service_date' => $request->service_date,
            'category'     => $request->category,
            'status'     => $request->status,
            'complaint'    => $request->complaint,
            'updated_by'   => Auth::id(),
        ]);

        // 4. Update mechanics
        $service->mechanics()->sync($request->mechanics);

        $total_cost = 0;

        // 5. Hapus jobs lama & insert baru
        $service->jobs()->delete();
        if ($request->has('jobs') && is_array($request->jobs)) {
            foreach ($request->jobs as $job) {
                if (!empty($job['job_name'])) {
                    $jobPrice = (float) ($job['job_price'] ?? 0);
                    $service->jobs()->create([
                        'job_name'  => $job['job_name'],
                        'job_price' => $jobPrice,
                    ]);
                    $total_cost += $jobPrice;
                }
            }
        }

        // 6. Hapus spareparts lama & insert baru
        $service->spareparts()->delete();
        if ($request->has('spareparts') && is_array($request->spareparts)) {
            foreach ($request->spareparts as $sp) {
                if (!empty($sp['sparepart_id'])) {
                    $sparepartId = (int) $sp['sparepart_id'];
                    $quantity    = (int) ($sp['quantity'] ?? 1);

                    $service->spareparts()->create([
                        'product_id' => $sparepartId,
                        'quantity'   => $quantity,
                    ]);
                }
            }
        }

        // 7. Update total_cost
        $service->update(['total_cost' => $total_cost]);

        DB::commit();
        return redirect()->route('services.index')->with('success', 'Service berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal update service: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Gagal update service. Detail: ' . $e->getMessage()])->withInput();
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
