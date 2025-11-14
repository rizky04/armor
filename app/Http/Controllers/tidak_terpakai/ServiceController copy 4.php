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
        $request->validate([
            'vehicle_id'   => 'required|exists:vehicles,id',
            'service_date' => 'required|date',
            'category'     => 'required|string',
            'complaint'    => 'nullable|string',
            'mechanics'    => 'required|array',
            'mechanics.*'  => 'exists:mechanics,id',
            'jobs'         => 'nullable|array',
            'spareparts'   => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // simpan service
            $service = Service::create([
                'vehicle_id'   => $request->vehicle_id,
                'service_date' => $request->service_date,
                'status'       => 'pending',
                'category'     => $request->category,
                'complaint'    => $request->complaint,
                'total_price'  => 0, // nanti dihitung
                'created_by'   => Auth::id(),
                'updated_by'   => Auth::id(),
            ]);

            // attach mechanics
            $service->mechanics()->sync($request->mechanics);

            $total = 0;

            // simpan jobs
            if ($request->has('jobs')) {
                foreach ($request->jobs as $job) {
                    if (!empty($job['job_name'])) {
                        $service->jobs()->create([
                            'job_name'  => $job['job_name'],
                            'job_price' => $job['job_price'] ?? 0,
                        ]);
                        $total += $job['job_price'] ?? 0;
                    }
                }
            }

            // simpan spareparts
            if ($request->has('spareparts')) {
                foreach ($request->spareparts as $sp) {
                    if (!empty($sp['sparepart_id'])) {
                        $service->spareparts()->create([
                            'sparepart_id' => $sp['sparepart_id'],
                            'quantity'     => $sp['quantity'] ?? 1,
                        ]);
                        // kalau mau hitung total harga sparepart
                        if (isset($sp['price'])) {
                            $total += ($sp['price'] * ($sp['quantity'] ?? 1));
                        }
                    }
                }
            }

            // update total harga
            $service->update(['total_price' => $total]);

            DB::commit();

            return redirect()->route('services.index')->with('success', 'Service berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('services.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
