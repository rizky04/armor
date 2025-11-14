<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use Illuminate\Http\Request;

class JasaController extends Controller
{
    public function index()
    {
        return view('jasa.index');
    }

    public function data(Request $request)
    {
        $query = Jasa::query();

        if ($request->search) {
            $query->where('nama_jasa', 'like', '%' . $request->search . '%');
        }
        $query->where('hapus', '0');

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jasa'  => 'required|string|max:255',
            'harga_jasa' => 'required|numeric|min:0',

        ]);

        $jasa = Jasa::create([
            'nama_jasa'  => $validated['nama_jasa'],
            'harga_jasa' => $validated['harga_jasa'],
            'jenis' => 'auto',
            'hapus'      => 0,
        ]);
        // $jasa = Jasa::create($request->only(['nama_jasa','harga_jasa']));
        return response()->json(['message' => 'Jasa berhasil ditambahkan', 'data' => $jasa]);
    }

    public function show($id)
    {
        return Jasa::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $jasa = Jasa::findOrFail($id);
        $jasa->update($request->only(['nama_jasa','harga_jasa']));
        return response()->json(['message' => 'Jasa berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $jasa = Jasa::findOrFail($id);
        $jasa->delete();
        return response()->json(['message' => 'Jasa berhasil dihapus']);
    }

     public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'nama_jasa'  => 'required|string|max:255',
            'harga_jasa' => 'required|numeric|min:0',

        ]);

        $jasa = Jasa::create([
            'nama_jasa'  => $validated['nama_jasa'],
            'harga_jasa' => $validated['harga_jasa'],
            'jenis' => 'manual',
            'hapus'      => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Jasa berhasil ditambahkan',
            'data' => $jasa
        ]);
    }
}
