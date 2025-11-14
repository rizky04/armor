<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Client;
use App\Models\Jasa;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Mechanic;
use App\Models\Product;

class Select2Controller extends Controller
{




    // 🔹 Sparepart (Product) untuk Select2
    public function products(Request $request)
    {
        $search = $request->get('q');
        $query = Product::query();

        if (!empty($search)) {
            $query->where('nama_barang', 'like', "%$search%")
                  ->orWhere('kode_barang', 'like', "%$search%")
                  ->orwhere('merk_barang', 'like', "%$search%");
        }



        $data = $query->limit(20)->get();


        // dd($products);

        // $data = [];
        // foreach ($products as $p) {
        //     $data[] = [
        //         'id'   => $p->id_barang,
        //       'text' => $p->nama_barang . ' (' . $p->kode_barang . ')' . ' - ' .  $p->merk_barang,
        //     ];
        // }

        return response()->json($data);
    }

       // 🔹 Mechanic untuk Select2
       public function mechanics(Request $request)
       {
           $search = $request->get('q');
           $query = Mechanic::query();

           if (!empty($search)) {
               $query->where('name', 'like', "%$search%");
           }

           $mechanics = $query->limit(20)->get();

           $data = [];
           foreach ($mechanics as $m) {
               $data[] = [
                   'id'   => $m->id,
                   'text' => $m->name . ' (' . $m->specialty . ')'
               ];
           }

           return response()->json($data);
       }

      // 🔹 Vehicle untuk Select2
      public function vehicles(Request $request)
      {
          $search = $request->get('q'); // kata kunci dari select2
          $query = Vehicle::with('client');

          if (!empty($search)) {
              $query->where('license_plate', 'like', "%$search%")
                    ->orWhereHas('client', function($q) use ($search) {
                        $q->where('nama_client', 'like', "%$search%");
                    });
          }

          $data = $query->limit(20)->get();

          return response()->json($data);
      }
    public function clients(Request $request)
    {
        $search = $request->get('q');
        $query = Client::query();
        if (!empty($search)) {
            $query->where('nama_client', 'like', "%$search%")
                  ->orWhere('no_telp', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%");
        }

        $data = $query->limit(20)->get();

        return response()->json($data);
    }

     public function jasa(Request $request)
    {
        $search = $request->get('q');
        $query = Jasa::query();
        if (!empty($search)) {
             $query->where('nama_jasa', 'like', "%$search%");
        }

        $data = $query->limit(100)->get();
        return response()->json($data);
    }

    // 🔹 Sparepart (Product) untuk Select2
    // public function barang(Request $request)
    // {
    //     $search = $request->get('q');
    //     $query = Barang::query();

    //     if (!empty($search)) {
    //         $query->where('id_barang', $search)
    //                ->orWhere('kode_barang', $search)
    //                ->orWhere('nama_barang', 'like', "%$search%")
    //                ->orwhere('merk_barang', 'like', "%$search%")
    //                ->orwhere('keterangan', 'like', "%$search%")
    //                ->orwhere('lokasi', 'like', "%$search%")
    //                ->orwhere('harga_jual', 'like', "%$search%")
    //                 ->orwhere('jenis', 'like', "%$search%");
    //     }


    //     $query->where('hapus', '0');
    //     $data = $query->get();
    //     // $data = $query->limit(20)->get();
    //     dd($data->toJson());

    //     return response()->json($data);
    // }

//     public function barang(Request $request)
// {
//     $search = $request->get('q');
//     $query = Barang::query();

//     if (!empty($search)) {
//         $query->where(function ($q) use ($search) {
//             $q->where('id_barang', $search)
//               ->orWhere('kode_barang', $search)
//               ->orWhere('nama_barang', 'like', "%$search%")
//               ->orWhere('merk_barang', 'like', "%$search%")
//               ->orWhere('keterangan', 'like', "%$search%")
//               ->orWhere('lokasi', 'like', "%$search%")
//               ->orWhere('harga_jual', 'like', "%$search%")
//               ->orWhere('jenis', 'like', "%$search%");
//         });
//     }

//     $query->where('hapus', '0');
//     $data = $query->get();

//     return response()->json($data);
// }

// public function barang(Request $request)
// {
//     $search = $request->get('q');
//     $query = Barang::query()
//         ->where('hapus', '0');

//     if (!empty($search)) {
//         $query->where(function ($q) use ($search) {
//             $q->where('nama_barang', 'like', "%{$search}%")
//             ->orWhere('id_barang', 'like', "%{$search}%")
//               ->orWhere('kode_barang', 'like', "%{$search}%")
//               ->orWhere('merk_barang', 'like', "%{$search}%")
//               ->orWhere('keterangan', 'like', "%{$search}%")
//               ->orWhere('jenis', 'like', "%{$search}%");
//         });
//     }

//     // Batasin jumlah hasil biar gak berat
//     // $data = $query->limit(20)->get();
//      $data = $query->get();

//     return response()->json($data);
// }

public function barang(Request $request)
{
    $search = $request->get('q');
    $query = Barang::query()->where('hapus', '0');

    if (!empty($search)) {
        // Pisahkan kata per spasi → contoh: "oli ertiga" jadi ['oli', 'ertiga']
        $keywords = explode(' ', $search);

        // Untuk setiap kata, cari di beberapa kolom
        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->where(function ($sub) use ($word) {
                    $sub->where('nama_barang', 'like', "%{$word}%")
                    ->orWhere('id_barang', 'like', "%{$word}%")
                        ->orWhere('kode_barang', 'like', "%{$word}%")
                        ->orWhere('merk_barang', 'like', "%{$word}%")
                        ->orWhere('keterangan', 'like', "%{$word}%")
                        ->orWhere('jenis', 'like', "%{$word}%");
                });
            }
        });
    }

    // Batasi hasil supaya tidak berat (misal 30 item)
    $data = $query->get();
    //  $data = $query->limit(30)->get();

    return response()->json($data);
}

public function barangSemua()
{
    $data = Barang::where('hapus', '0')->get();

    return response()->json($data);
}



}
