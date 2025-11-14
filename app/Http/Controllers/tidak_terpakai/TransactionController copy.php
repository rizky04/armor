<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $products = Product::all();
        $categories = Category::all();
        return view('transaction.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'total'            => 'required|numeric',
            'discount'         => 'required|numeric',
            'tax'              => 'required|numeric',
            'total_after_tax'  => 'required|numeric',
            'cash'             => 'required|numeric',
            'change'           => 'required|numeric',
            'items'            => 'required|array',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.qty'      => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric',
            'items.*.subtotal' => 'required|numeric',
            'plate_photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'customer_id'     => $validated['customer_id'],
                'total'           => $validated['total'],
                'discount'        => $validated['discount'],
                'tax'             => $validated['tax'],
                'total_after_tax' => $validated['total_after_tax'],
                'cash'            => $validated['cash'],
                'change'          => $validated['change'],
            ]);

            foreach ($validated['items'] as $item) {
                $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }

            if ($request->hasFile('plate_photo')) {
                $imageName = time() . '.' . $request->plate_photo->extension();
                $request->plate_photo->move(public_path('uploads/plates'), $imageName);

                $transaction->plate_photo = $imageName;
                $transaction->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan dengan nomor ' . $transaction->reference,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            // hapus item dulu kalau ada relasi
            $transaction->items()->delete();

            // hapus transaksi
            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }


    public function detail(Request $request)
    {
        // $transactions = Transaction::with('customer')->latest()->get();
        // // dd($transactions);

        // return response()->json([
        //     'success' => true,
        //     'data' => $transactions->map(function ($trx) {
        //         return [
        //             'id' => $trx->id,
        //             'date' => $trx->created_at->format('Y-m-d'),
        //             'reference' => $trx->reference,
        //             'customer' => $trx->customer->name ?? 'Walk-in Customer',
        //             'plate_number' => $trx->customer->plate_number ?? '-',
        //             'amount' => $trx->total_after_tax,
        //         ];
        //     }),
        // ]);
    //     $query = Transaction::with('customer');

    // if ($request->has('search') && $request->search != '') {
    //     $search = $request->search;
    //     $query->where('reference', 'LIKE', "%$search%")
    //           ->orWhereHas('customer', function($q) use ($search) {
    //               $q->where('name', 'LIKE', "%$search%");
    //           });
    // }

    // $transactions = $query->latest()->get();

    // return response()->json([
    //     'success' => true,
    //     'data' => $transactions
    // ]);
    // $query = Transaction::with('customer');

    // // 🔍 Filter search
    // if ($request->has('search') && $request->search != '') {
    //     $search = $request->search;
    //     $query->where(function($q) use ($search) {
    //         $q->where('reference', 'LIKE', "%$search%")
    //           ->orWhereHas('customer', function($q2) use ($search) {
    //               $q2->where('name', 'LIKE', "%$search%");
    //           });
    //     });
    // }

    // // 📅 Filter tanggal
    // if ($request->has('start_date') && $request->has('end_date') &&
    //     $request->start_date != '' && $request->end_date != '') {
    //     $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
    // }

    // // 🔹 Pagination
    // $perPage = $request->get('per_page', 5);
    // $transactions = $query->latest()->paginate($perPage);

    // return response()->json($transactions);
    $query = Transaction::with('customer');

    // Filter pencarian
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('reference', 'LIKE', "%$search%")
              ->orWhereHas('customer', function($q) use ($search) {
                  $q->where('name', 'LIKE', "%$search%");
              });
    }

    // Filter range tanggal
    if ($request->has('start_date') && $request->start_date != '') {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    if ($request->has('end_date') && $request->end_date != '') {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    // Pagination
    $perPage = $request->get('per_page', 10); // default 10
    $transactions = $query->latest()->paginate($perPage);

    // Response JSON siap JS
    return response()->json([
        'success' => true,
        'data' => $transactions->items(), // isi halaman sekarang
        'meta' => [
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'per_page' => $transactions->perPage(),
            'total' => $transactions->total(),
        ]
    ]);
    }
}
