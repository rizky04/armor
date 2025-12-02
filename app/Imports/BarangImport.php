<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        dd($row);
        // JIKA KODE_BARANG KOSONG → SKIP BARIS
        if (!isset($row['kode_barang']) || empty($row['kode_barang'])) {
            return null;
        }

        return Barang::updateOrCreate(
            ['kode_barang' => $row['kode_barang']],

            [
                'nama_barang'   => $row['nama_barang']   ?? '',
                'merk_barang'   => $row['merk_barang']   ?? '',
                'keterangan'    => $row['keterangan']    ?? '',
                'lokasi'        => $row['lokasi']        ?? '',
                'stok_barang'   => $row['stok']          ?? 0,
                'pagu'          => $row['pagu']          ?? 0,
                'harga_kulak'   => $row['harga_kulak']   ?? 0,
                'harga_jual'    => $row['harga_jual']    ?? 0,
                'distributor'   => $row['distributor']   ?? '',
                'jenis'         => $row['jenis']         ?? '',
            ]
        );
    }
}
