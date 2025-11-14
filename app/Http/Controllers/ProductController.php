<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('products.index');
    }

    public function getData(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_barang', 'like', "%{$request->search}%")
                ->orWhere('kode_barang', 'like', "%{$request->search}%")
                ->orWhere('merk_barang', 'like', "%{$request->search}%");
        }

        $products = $query->orderBy('stok_barang', 'asc')->paginate(15);

        return response()->json($products);
    }

    // app/Http/Controllers/SparepartController.php
    public function search(Request $request)
    {
        $search = $request->get('q');

        $spareparts = Product::query()
            ->where('nama_barang', 'like', "%$search%")
            ->Orwhere('kode_barang', 'like', "%$search%")
            ->Orwhere('merk_barang', 'like', "%$search%")
            ->limit(20)
            ->get();

        $results = $spareparts->map(function ($sp) {
            return [
                'id' => $sp->id,
                'text' => $sp->nama_barang . ' (' . $sp->kode_barang . ')' . ' - ' .  $sp->merk_barang,
            ];
        });

        return response()->json(['results' => $results]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:tbl_barang',
            'nama_barang' => 'required',
            'merk_barang' => 'required',
            'keterangan' => 'required',
            'lokasi' => 'required',
            'stok_barang' => 'required',
            'pagu' => 'required',
            'harga_kulak' => 'required',
            'harga_jual' => 'required',
            'distributor' => 'required',
            'jenis' => 'required',
            'hapus' => 'required',
        ]);

        Product::create($request->all());

        return response()->json(['success' => true, 'message' => 'Product berhasil ditambahkan']);
    }
     public function show($id)
    {
        return Product::findOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json(['success' => true, 'message' => 'Product berhasil diupdate']);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Product berhasil dihapus']);
    }
}
