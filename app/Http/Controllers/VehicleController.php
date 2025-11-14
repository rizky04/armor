<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class VehicleController extends Controller
{
    public function index()
    {
        return view('vehicles.index');
    }

    public function getData(Request $request)
    {
        $query = Vehicle::with('client');

        if ($request->has('search') && $request->search != '') {
            $query->where('license_plate', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%")
                  ->orWhere('type', 'like', "%{$request->search}%")
                  ->orWhereHas('client', function($q) use ($request) {
                        $q->where('nama_client', 'like', "%{$request->search}%");
                  });
        }

        $vehicles = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json($vehicles);
    }

    public function getVehicles(Request $request)
{
    $query = Vehicle::with('client');

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('license_plate', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhereHas('client', function ($q) use ($search) {
                  $q->where('nama_client', 'like', "%{$search}%");
              });
    }

    $vehicles = $query->limit(20)->get();

    $results = $vehicles->map(function ($v) {
        return [
            'id'   => $v->id,
            'text' => "{$v->license_plate} - {$v->brand} ({$v->client->nama_client})"
        ];
    });

    return response()->json(['results' => $results]);
}


    public function store(Request $request)
{
    // dd($request->all());
    $request->validate([
        'id_client'   => 'required|exists:tbl_client,id_client',
        'license_plate' => 'required|unique:vehicles',
        'brand'         => 'nullable|string|max:100',
        'type'          => 'nullable|string|max:100',
        'engine_number' => 'nullable|string|max:100',
        'chassis_number'=> 'nullable|string|max:100',
        'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:10048',
    ]);

    $vehicle = new Vehicle($request->except('photo'));


    // if ($request->hasFile('photo')) {
    //     $imageName = time() . '.' . $request->photo->extension();
    //     $request->photo->move(public_path('uploads/vehicle'), $imageName);
    //     $vehicle->photo = $imageName;
    // }

    if ($request->hasFile('photo')) {
        $imageName = time() . '.' . $request->photo->extension();

        // buat manager pakai GD driver
        $manager = new ImageManager(new Driver());

        // buka file, resize, dan simpan
        $img = $manager->read($request->file('photo'))
            ->scale(width: 800) // resize width 800px, height otomatis proporsional
            ->toJpeg(75);       // convert ke JPEG kualitas 75%

        // simpan ke folder
        $img->save(public_path('uploads/vehicle/' . $imageName));

        // simpan nama file ke database
        $vehicle->photo = $imageName;
    }



    $vehicle->save();
        // pastikan relasi client dimuat
    $vehicle->load('client');

    return response()->json([
        'success' => true,
        'status'  => true, // <- ubah dari success ke status agar cocok dengan JS
        'message' => 'Kendaraan baru berhasil ditambahkan',
        'data'    => [
            'id'             => $vehicle->id,
            'license_plate'  => $vehicle->license_plate,
            'id_client'      => $vehicle->id_client,
            'client' => [
                'nama_client' => optional($vehicle->client)->nama_client,
            ],
        ],
    ]);

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Kendaraan berhasil ditambahkan',
    //     'data'    => $vehicle
    // ]);
}

public function update(Request $request, $id)
{
    $vehicle = Vehicle::findOrFail($id);

    $request->validate([
       'id_client'   => 'required|exists:tbl_client,id_client',
        'license_plate' => 'required|unique:vehicles,license_plate,' . $vehicle->id,
        'brand'         => 'nullable|string|max:100',
        'type'          => 'nullable|string|max:100',
        'engine_number' => 'nullable|string|max:100',
        'chassis_number'=> 'nullable|string|max:100',
        'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:10048',
    ]);

    $vehicle->fill($request->except('photo'));

    // simpan foto plat baru
    if ($request->hasFile('photo')) {
        $imageName = time() . '.' . $request->photo->extension();

        // buat manager pakai GD driver
        $manager = new ImageManager(new Driver());

        // buka file, resize, dan simpan
        $img = $manager->read($request->file('photo'))
            ->scale(width: 800) // resize width 800px, height otomatis proporsional
            ->toJpeg(75);       // convert ke JPEG kualitas 75%

        // simpan ke folder
        $img->save(public_path('uploads/vehicle/' . $imageName));

        // simpan nama file ke database
        $vehicle->photo = $imageName;
    }

    $vehicle->save();

    return response()->json([
        'success' => true,
        'message' => 'Kendaraan berhasil diupdate',
        'data'    => $vehicle
    ]);
}


    public function show($id)
    {
        $vehicle = Vehicle::with('client')->findOrFail($id);
        return response()->json($vehicle);
    }


    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // hapus file foto kalau ada
        if ($vehicle->photo && file_exists(public_path('uploads/vehicle/' . $vehicle->photo))) {
            unlink(public_path('uploads/vehicle/' . $vehicle->photo));
        }

        // hapus data dari database
        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle dan fotonya berhasil dihapus'
        ]);
    }

}
