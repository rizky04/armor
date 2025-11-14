<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function daftarTransaksi()
    {
        $customers = Customer::all();
        $products = Product::all();
        $categories = Category::all();
        return view('transactions.index', compact('customers', 'products', 'categories'));
    }

    public function getData(Request $request)
    {
        $columns = ['reference', 'customer_id', 'total', 'date'];

        $query = Transaction::with('customer');

        // 🔎 filter search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 📅 filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        // total data sebelum filter
        $recordsTotal = Transaction::count();

        // total data setelah filter
        $recordsFiltered = $query->count();

        // urutan data
        if ($request->has('order')) {
            $orderColIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');
            $orderCol = $columns[$orderColIndex] ?? 'date';
            $query->orderBy($orderCol, $orderDir);
        } else {
            $query->latest();
        }

        // pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $data = $query->skip($start)->take($length)->get();

        // response DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($trx) {
                return [
                    'id' => $trx->id,
                    'reference' => $trx->reference,
                    'customer' => [
                        'name' => $trx->customer->name ?? '-'
                    ],
                    'total' => number_format($trx->total, 0, ',', '.'),
                    'date' => $trx->created_at->format('Y-m-d H:i'),
                ];
            }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'total'              => 'required|numeric',
            'discount'           => 'required|numeric',
            'tax'                => 'required|numeric',
            'total_after_tax'    => 'required|numeric',
            'cash'               => 'required|numeric',
            'payment_method'    => 'required|string|in:cash,debit,qris',
            'change'             => 'required|numeric',
            'items'              => 'required|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.subtotal'   => 'required|numeric|min:0',
            'plate_photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

        if (empty($items) || count($items) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi harus memiliki minimal 1 produk.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($validated['customer_id']);
            $promo    = Promo::where('is_active', 1)->first();
            $isFree   = false;
            $message  = 'Transaksi berhasil disimpan';

            // load semua produk sekali saja
            $productIds = collect($items)->pluck('product_id')->toArray();
            $products   = Product::with('category')->whereIn('id', $productIds)->get()->keyBy('id');


            $hasCuci     = false;

            // hitung total
            foreach ($items as $item) {
                $product  = $products[$item['product_id']] ?? null;

                if ($product && $product->category && strtolower($product->category->name) === 'cuci') {
                    $hasCuci = true;

                }
            }



            // logika voucher & promo
            if ($promo) {
                if ($customer->free_voucher > 0 && $hasCuci) {
                    $isFree = true;
                    $customer->free_voucher -= 1;
                    $message = 'Transaksi berhasil. 1 voucher digunakan. Sisa voucher: ' . $customer->free_voucher;
                } else {
                    if ($hasCuci) {
                        $customer->squence += 1;
                        if ($customer->squence >= $promo->buy_count) {
                            $customer->squence = 0;
                            $customer->free_voucher += $promo->free_count ?? 1;
                            $message = "Transaksi berhasil. Anda mendapatkan voucher gratis!";
                        }
                    }
                }
                $customer->save();
            }

            // buat transaksi
            $transaction = Transaction::create([
                'customer_id'     => $validated['customer_id'],
                'created_by'         => Auth::user()->id,
                'total'           =>  $validated['total'],
                'discount'        => $validated['discount'],
                'tax'             => $validated['tax'],
                'total_after_tax' => $validated['total_after_tax'],
                'cash'            => $validated['cash'],
                 'payment_method'  => $validated['payment_method'],
                'change'          => $validated['change'],
                'date'            => now(),
            ]);

            // simpan detail items
            foreach ($items as $item) {
                $product = $products[$item['product_id']] ?? null;
                $isCuci  = $product && $product->category && strtolower($product->category->name) === 'cuci';

                $price    = $item['price'];
                $subtotal = $item['subtotal'];

                if ($isFree && $isCuci) {
                    $price = 0;
                    $subtotal = 0;
                }

                $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                ]);
            }

            // simpan foto plat
            if ($request->hasFile('plate_photo')) {
                $imageName = time() . '.' . $request->plate_photo->extension();
                $request->plate_photo->move(public_path('uploads/plates'), $imageName);
                $transaction->plate_photo = $imageName;
                $transaction->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_free' => $isFree,
                'data'    => $transaction->load(['items.product.category', 'customer'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['items.product', 'customer', 'createdBy', 'updatedBy'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
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
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'total'            => 'required|numeric',
            'discount'         => 'required|numeric',
            'tax'              => 'required|numeric',
            'total_after_tax'  => 'required|numeric',
            'cash'             => 'required|numeric',
            'payment_method'   => 'nullable|string|in:cash,debit,qris',
            'change'           => 'required|numeric',
            'items'            => 'required',
            'plate_photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $items = json_decode($request->items, true);
        if (!is_array($items)) {
            return response()->json(['success' => false, 'message' => 'Invalid items format'], 422);
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            $transaction->update([
                'customer_id'     => $validated['customer_id'],
                'total'           => $validated['total'],
                'discount'        => $validated['discount'],
                'tax'             => $validated['tax'],
                'total_after_tax' => $validated['total_after_tax'],
                'cash'            => $validated['cash'],
                'payment_method'  => $validated['payment_method'],
                'change'          => $validated['change'],
                'updated_by'         => Auth::user()->id,
            ]);

            // hapus semua item lama
            $transaction->items()->delete();

            // insert ulang items
            foreach ($items as $item) {
                $transaction->items()->create($item);
            }

            // replace foto plat kalau ada upload baru
            if ($request->hasFile('plate_photo')) {
                if ($transaction->plate_photo && file_exists(public_path('uploads/plates/' . $transaction->plate_photo))) {
                    unlink(public_path('uploads/plates/' . $transaction->plate_photo));
                }

                $imageName = time() . '.' . $request->plate_photo->extension();
                $request->plate_photo->move(public_path('uploads/plates'), $imageName);
                $transaction->plate_photo = $imageName;
                $transaction->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui dengan nomor ' . $transaction->reference,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
        $query = Transaction::with('customer');

        // Filter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('reference', 'LIKE', "%$search%")
                ->orWhereHas('customer', function ($q) use ($search) {
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

            public function print($id)
        {
            $transaction = Transaction::with(['items.product', 'customer'])->findOrFail($id);

            return view('transactions.print', compact('transaction'));
        }

}
