<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use Illuminate\Http\Request;

class MechanicController extends Controller
{
    public function index()
    {
        return view('mechanics.index');
    }

    public function getData(Request $request)
    {
        $query = Mechanic::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('specialty', 'like', "%{$request->search}%");
        }

        $mechanics = $query->orderBy('id', 'desc')->paginate(10);

        return response()->json($mechanics);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $mechanic = Mechanic::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mechanic berhasil ditambahkan',
            'data' => $mechanic
        ]);
    }

    public function show($id)
    {
        $mechanic = Mechanic::findOrFail($id);
        return response()->json($mechanic);
    }

    public function update(Request $request, $id)
    {
        $mechanic = Mechanic::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $mechanic->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mechanic berhasil diupdate',
            'data' => $mechanic
        ]);
    }

    public function destroy($id)
    {
        $mechanic = Mechanic::findOrFail($id);
        $mechanic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mechanic berhasil dihapus'
        ]);
    }
}

