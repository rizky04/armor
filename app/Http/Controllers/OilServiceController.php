<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\OilService;
use Illuminate\Http\Request;

class OilServiceController extends Controller
{
    public function index()
    {
        $oilServices = OilService::with(['service.vehicle'])
            ->orderBy('service_date', 'desc')
            ->paginate(20);

        return view('oil_services.index', compact('oilServices'));
    }

    public function getServicesWithOil(Request $request)
    {
        $search = $request->get('q');

        $query = Service::with(['vehicle', 'spareparts.barang'])
            ->whereHas('spareparts.barang', function ($q) {
                $q->where('nama_barang', 'like', '%oli%');
            });

        if (!empty($search)) {
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%");
            })
            ->orWhere('nomor_service', 'like', "%{$search}%");
        }

        $services = $query->limit(15)->get();

        $results = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'text' => $service->nomor_service . ' - ' . ($service->vehicle->license_plate ?? 'Tanpa Nopol'),
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function getOilNamesByService($service_id)
{
    $service = Service::with('spareparts.barang')->findOrFail($service_id);

    // dd($service);

    // Ambil semua sparepart yang namanya mengandung "oli"
    $oilNames = $service->spareparts
        ->filter(fn($s) => stripos($s->barang->nama_barang, 'oli') !== false)
        ->pluck('barang.nama_barang')
        ->values();

    return response()->json(['oils' => $oilNames]);
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'km_service' => 'required|integer',
            'km_service_next' => 'nullable|integer',
            'next_service_date' => 'nullable|date',
            'oil_name' => 'required|string',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $oilService = OilService::create([
            'service_id' => $service->id,
            'service_date' => $service->service_date,
            'km_service' => $validated['km_service'],
            'km_service_next' => $validated['km_service_next'],
            'next_service_date' => $validated['next_service_date'],
            'oil_name' => $validated['oil_name'],
        ]);

        return response()->json(['success' => true, 'data' => $oilService]);
    }

    public function edit($id)
    {
        $oilService = OilService::with('service.vehicle')->findOrFail($id);
        return response()->json($oilService);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'km_service' => 'required|integer',
            'km_service_next' => 'nullable|integer',
            'next_service_date' => 'nullable|date',
            'oil_name' => 'required|string',
        ]);

        $oilService = OilService::findOrFail($id);
        $oilService->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $oilService = OilService::findOrFail($id);
        $oilService->delete();

        return response()->json(['success' => true]);
    }

    public function print($id)
{
    $oilService = OilService::with(['service.vehicle'])->findOrFail($id);
    return view('oil_services.print', compact('oilService'));
}
}
