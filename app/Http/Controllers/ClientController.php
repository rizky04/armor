<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;


class ClientController extends Controller
{

    public function index()
    {
        return view('client.index');
    }

    // 🔹 Ambil data untuk DataTable / AJAX
    public function data(Request $request)
    {
        $query = Client::query()->orderBy('id_client', 'desc');

        if ($request->search) {
            $query->where('nama_client', 'like', "%{$request->search}%")
                ->orWhere('no_telp', 'like', "%{$request->search}%")
                ->orWhere('no_ktp', 'like', "%{$request->search}%")
                ->orWhere('alamat', 'like', "%{$request->search}%");
        }

        $clients = $query->paginate(5); // bisa diubah jumlah per halaman
        return response()->json($clients);
    }

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_client' => 'required|string|max:255',
            'no_telp'     => 'required|string|max:20',
            'no_ktp'      => 'required|string|max:50',
            'alamat'      => 'required|string',
            'hapus'       => 'nullable|boolean',
        ]);

        $request['hapus'] = '0';

        $client = Client::create($request->all());

        return response()->json([
            'message' => 'Client berhasil ditambahkan',
            'data'    => $client,
        ]);
    }

    // 🔹 Ambil detail client untuk edit
    public function show($id)
    {
        return Client::findOrFail($id);
    }

    // 🔹 Update data client
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_client' => 'required|string|max:255',
            'no_telp'     => 'required|string|max:20',
            'no_ktp'      => 'required|string|max:50',
            'alamat'      => 'required|string',
            'hapus'       => 'nullable|boolean',
        ]);

       $client = Client::findOrFail($id);
       $client->hapus = '0';
        $client->update($request->all());

        return response()->json([
            'message' => 'Client berhasil diperbarui',
            'data'    => $client,
        ]);
    }

    // 🔹 Hapus data client
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'message' => 'Client berhasil dihapus'
        ]);
    }


}
