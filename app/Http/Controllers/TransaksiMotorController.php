<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Jasa;
use App\Models\TransaksiMotor;
use App\Models\TransaksiMotorBarang;
use App\Models\TransaksiMotorJasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiMotorController extends Controller
{
    public function index()
    {
        return view('transaksi_motor.index');
    }

    public function getData(Request $request)
    {
        $query = TransaksiMotor::with('creator')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_transaksi', 'like', "%$q%")
                    ->orWhere('nama_customer', 'like', "%$q%")
                    ->orWhere('plat_nomor', 'like', "%$q%");
            });
        }

        $data = $query->get()->map(function ($item) {
            return [
                'id'               => $item->id,
                'nomor_transaksi'  => $item->nomor_transaksi,
                'nama_customer'    => $item->nama_customer,
                'plat_nomor'       => $item->plat_nomor ?? '-',
                'nama_motor'       => $item->nama_motor ?? '-',
                'tanggal'          => $item->tanggal,
                'status'             => $item->status,
                'metode_pembayaran'  => $item->metode_pembayaran,
                'total'              => $item->total,
                'total_fmt'          => 'Rp ' . number_format($item->total, 0, ',', '.'),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function create()
    {
        return view('transaksi_motor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'tanggal'       => 'required|date',
        ]);

        $barangItems = json_decode($request->barang_items ?? '[]', true);
        $jasaItems   = json_decode($request->jasa_items ?? '[]', true);

        if (empty($barangItems) && empty($jasaItems)) {
            return response()->json(['message' => 'Minimal tambahkan 1 barang atau 1 jasa.'], 422);
        }

        DB::beginTransaction();
        try {
            $totalBarang = 0;
            $totalJasa   = 0;

            foreach ($barangItems as $item) {
                $totalBarang += (float)$item['subtotal'];
            }
            foreach ($jasaItems as $item) {
                $totalJasa += (float)$item['subtotal'];
            }

            $transaksi = TransaksiMotor::create([
                'nomor_transaksi' => TransaksiMotor::generateNomor(),
                'nama_customer'   => $request->nama_customer,
                'no_hp'           => $request->no_hp,
                'plat_nomor'      => $request->plat_nomor,
                'nama_motor'      => $request->nama_motor,
                'tanggal'         => $request->tanggal,
                'catatan'         => $request->catatan,
                'status'             => $request->status ?? 'selesai',
                'metode_pembayaran'  => $request->metode_pembayaran ?? 'cash',
                'total_barang'    => $totalBarang,
                'total_jasa'      => $totalJasa,
                'total'           => $totalBarang + $totalJasa,
                'created_by'      => Auth::id(),
            ]);

            foreach ($barangItems as $item) {
                TransaksiMotorBarang::create([
                    'transaksi_motor_id' => $transaksi->id,
                    'barang_id'          => $item['id_barang'],
                    'kode_barang'        => $item['kode_barang'] ?? null,
                    'nama_barang'        => $item['nama_barang'],
                    'harga'              => $item['harga'],
                    'harga_kulak'        => $item['harga_kulak'] ?? 0,
                    'qty'                => $item['qty'],
                    'subtotal'           => $item['subtotal'],
                ]);

                // Potong stok untuk semua status (draft & selesai) — stok direservasi sejak transaksi dibuat
                Barang::where('id_barang', $item['id_barang'])
                      ->decrement('stok_barang', $item['qty']);
            }

            foreach ($jasaItems as $item) {
                $jasaId = $item['id_jasa'];

                if (!empty($item['is_manual'])) {
                    $jasa = Jasa::firstOrCreate(
                        ['nama_jasa' => $item['nama_jasa']],
                        [
                            'kode_jasa'  => null,
                            'harga_jasa' => $item['harga'],
                            'jenis'      => 'manual',
                            'hapus'      => 0,
                        ]
                    );
                    $jasaId = $jasa->id_jasa;
                }

                TransaksiMotorJasa::create([
                    'transaksi_motor_id' => $transaksi->id,
                    'jasa_id'            => $jasaId,
                    'kode_jasa'          => $item['kode_jasa'] ?? null,
                    'nama_jasa'          => $item['nama_jasa'],
                    'harga'              => $item['harga'],
                    'qty'                => $item['qty'],
                    'subtotal'           => $item['subtotal'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Transaksi berhasil disimpan.', 'data' => $transaksi]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $transaksi = TransaksiMotor::with(['barangs', 'jasas'])->findOrFail($id);

        if ($transaksi->status !== 'draft') {
            return redirect()->route('transaksi-motor.show', $id)
                ->with('error', 'Hanya transaksi Draft yang bisa diedit.');
        }

        return view('transaksi_motor.edit', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'tanggal'       => 'required|date',
        ]);

        $barangItems = json_decode($request->barang_items ?? '[]', true);
        $jasaItems   = json_decode($request->jasa_items ?? '[]', true);

        if (empty($barangItems) && empty($jasaItems)) {
            return response()->json(['message' => 'Minimal tambahkan 1 barang atau 1 jasa.'], 422);
        }

        $transaksi = TransaksiMotor::with('barangs')->findOrFail($id);

        if ($transaksi->status !== 'draft') {
            return response()->json(['message' => 'Hanya transaksi Draft yang bisa diedit.'], 403);
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok lama dulu (sudah dipotong saat transaksi dibuat), lalu dipotong ulang di bawah
            foreach ($transaksi->barangs as $old) {
                Barang::where('id_barang', $old->barang_id)->increment('stok_barang', $old->qty);
            }
            $transaksi->barangs()->delete();
            $transaksi->jasas()->delete();

            $totalBarang = 0;
            $totalJasa   = 0;

            foreach ($barangItems as $item) {
                $totalBarang += (float)$item['subtotal'];
            }
            foreach ($jasaItems as $item) {
                $totalJasa += (float)$item['subtotal'];
            }

            $newStatus = $request->status ?? 'draft';

            $transaksi->update([
                'nama_customer'    => $request->nama_customer,
                'no_hp'            => $request->no_hp,
                'plat_nomor'       => $request->plat_nomor,
                'nama_motor'       => $request->nama_motor,
                'tanggal'          => $request->tanggal,
                'catatan'          => $request->catatan,
                'status'           => $newStatus,
                'metode_pembayaran'=> $request->metode_pembayaran ?? 'cash',
                'total_barang'     => $totalBarang,
                'total_jasa'       => $totalJasa,
                'total'            => $totalBarang + $totalJasa,
            ]);

            foreach ($barangItems as $item) {
                TransaksiMotorBarang::create([
                    'transaksi_motor_id' => $transaksi->id,
                    'barang_id'          => $item['id_barang'],
                    'kode_barang'        => $item['kode_barang'] ?? null,
                    'nama_barang'        => $item['nama_barang'],
                    'harga'              => $item['harga'],
                    'harga_kulak'        => $item['harga_kulak'] ?? 0,
                    'qty'                => $item['qty'],
                    'subtotal'           => $item['subtotal'],
                ]);

                // Potong stok untuk semua status (draft & selesai)
                Barang::where('id_barang', $item['id_barang'])
                      ->decrement('stok_barang', $item['qty']);
            }

            foreach ($jasaItems as $item) {
                $jasaId = $item['id_jasa'];

                if (!empty($item['is_manual'])) {
                    $jasa = Jasa::firstOrCreate(
                        ['nama_jasa' => $item['nama_jasa']],
                        [
                            'kode_jasa'  => null,
                            'harga_jasa' => $item['harga'],
                            'jenis'      => 'manual',
                            'hapus'      => 0,
                        ]
                    );
                    $jasaId = $jasa->id_jasa;
                }

                TransaksiMotorJasa::create([
                    'transaksi_motor_id' => $transaksi->id,
                    'jasa_id'            => $jasaId,
                    'kode_jasa'          => $item['kode_jasa'] ?? null,
                    'nama_jasa'          => $item['nama_jasa'],
                    'harga'              => $item['harga'],
                    'qty'                => $item['qty'],
                    'subtotal'           => $item['subtotal'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Transaksi berhasil diperbarui.', 'data' => $transaksi]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $transaksi = TransaksiMotor::with(['barangs', 'jasas', 'creator'])->findOrFail($id);
        return view('transaksi_motor.show', compact('transaksi'));
    }

    public function destroy($id)
    {
        $transaksi = TransaksiMotor::with('barangs')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Kembalikan stok (dipotong untuk semua status draft & selesai)
            foreach ($transaksi->barangs as $item) {
                Barang::where('id_barang', $item->barang_id)
                      ->increment('stok_barang', $item->qty);
            }
            $transaksi->delete();
            DB::commit();
            return response()->json(['message' => 'Transaksi berhasil dihapus.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }

    public function print($id)
    {
        $transaksi = TransaksiMotor::with(['barangs', 'jasas'])->findOrFail($id);
        return view('transaksi_motor.print', compact('transaksi'));
    }

    public function laporan()
    {
        return view('transaksi_motor.laporan');
    }

    public function laporanData(Request $request)
    {
        $query = TransaksiMotor::with(['barangs', 'jasas'])->where('status', 'selesai');

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $data = $query->orderBy('tanggal')->get();

        $totalOmzetBarang    = 0;
        $totalOmzetJasa      = 0;
        $totalModalBarang    = 0;
        $totalUntungBarang   = 0;
        $totalUntungJasa     = 0;

        $rows = $data->map(function ($trx) use (
            &$totalOmzetBarang, &$totalOmzetJasa,
            &$totalModalBarang, &$totalUntungBarang, &$totalUntungJasa
        ) {
            $omzetBarang  = 0;
            $modalBarang  = 0;
            $untungBarang = 0;

            foreach ($trx->barangs as $b) {
                $omzetBarang  += $b->subtotal;
                $modal         = $b->harga_kulak * $b->qty;
                $modalBarang  += $modal;
                $untungBarang += $b->subtotal - $modal;
            }

            $omzetJasa  = $trx->total_jasa;
            // Jasa = 100% keuntungan (tidak ada modal)
            $untungJasa = $omzetJasa;

            $totalOmzetBarang  += $omzetBarang;
            $totalOmzetJasa    += $omzetJasa;
            $totalModalBarang  += $modalBarang;
            $totalUntungBarang += $untungBarang;
            $totalUntungJasa   += $untungJasa;

            return [
                'id'              => $trx->id,
                'nomor_transaksi' => $trx->nomor_transaksi,
                'tanggal'         => $trx->tanggal,
                'nama_customer'   => $trx->nama_customer,
                'plat_nomor'      => $trx->plat_nomor,
                'nama_motor'      => $trx->nama_motor,
                'barangs'         => $trx->barangs,
                'jasas'           => $trx->jasas,
                'omzet_barang'    => $omzetBarang,
                'modal_barang'    => $modalBarang,
                'untung_barang'   => $untungBarang,
                'omzet_jasa'      => $omzetJasa,
                'untung_jasa'     => $untungJasa,
                'total'           => $trx->total,
                'keuntungan'      => $untungBarang + $untungJasa,
            ];
        });

        return response()->json([
            'data'               => $rows,
            'total_omzet_barang' => $totalOmzetBarang,
            'total_omzet_jasa'   => $totalOmzetJasa,
            'total_modal'        => $totalModalBarang,
            'total_untung_barang'=> $totalUntungBarang,
            'total_untung_jasa'  => $totalUntungJasa,
            'grand_omzet'        => $totalOmzetBarang + $totalOmzetJasa,
            'grand_keuntungan'   => $totalUntungBarang + $totalUntungJasa,
        ]);
    }
}
