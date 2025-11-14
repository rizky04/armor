<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::all();
        return response()->json($promos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'buy_count' => 'required|integer|min:1',
            'free_count' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $promo = Promo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil dibuat',
            'data' => $promo
        ]);
    }

    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'buy_count' => 'sometimes|integer|min:1',
            'free_count' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        $promo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil diperbarui',
            'data' => $promo
        ]);
    }

    public function toggleActive($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->is_active = !$promo->is_active;
        $promo->save();

        return response()->json([
            'success' => true,
            'message' => $promo->is_active ? 'Promo diaktifkan' : 'Promo dinonaktifkan',
            'data' => $promo
        ]);
    }

    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil dihapus'
        ]);
    }

    public function tampilanPromo(){
        return view('promo.index');
    }
}
