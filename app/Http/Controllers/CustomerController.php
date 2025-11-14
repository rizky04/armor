<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('customer.index');
    }

  public function getData(Request $request)
{
    $query = Customer::query();

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('no_telp', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }

    $customers = $query->orderBy('id', 'desc')->paginate(2);

    return response()->json($customers);
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
            'name'    => 'required|string|max:255',
            'no_telp' => 'required|string|max:20|unique:customers,no_telp',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan',
            'customer' => $customer
        ]);
    }







    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'no_telp' => 'required|string|max:20|unique:customers,no_telp,' . $id,
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diperbarui',
            'customer' => $customer
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus'
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $customers = Customer::where('name', 'like', "%$search%")
            ->orWhere('no_telp', 'like', "%$search%")
            ->orWhere('address', 'like', "%$search%")
            ->limit(20)
            ->get();

        return response()->json($customers);
    }

    public function promoCheck($id)
    {
        $customer = Customer::findOrFail($id);
        $promo = Promo::where('is_active', 1)->first();

        $info = [
            'squence'      => $customer->squence,
            'free_voucher'  => $customer->free_voucher,
            'eligible_free' => false,
            'message'       => null,
        ];

        if ($promo) {
            if ($customer->free_voucher > 0) {
                $info['eligible_free'] = true;
                $info['message'] = "Customer punya {$customer->free_voucher} voucher gratis.";
            } elseif ($customer->squence >= $promo->buy_count) {
                $info['eligible_free'] = true;
                $info['message'] = "Transaksi ini GRATIS (promo aktif setelah {$promo->buy_count}x).";
            } else {
                $info['message'] = "Customer baru cuci {$customer->squence}x, promo berlaku di transaksi ke-{$promo->buy_count}.";
            }
        }

        return response()->json($info);
    }
}
