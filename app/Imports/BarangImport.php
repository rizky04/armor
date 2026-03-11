<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan nama key array ($row['...']) sesuai dengan header di Excel (huruf kecil, spasi diganti underscore biasanya, tergantung settingan excel slug)
        // Library ini biasanya otomatis mengubah header "Kode Barang" menjadi "kode_barang"

        return Barang::updateOrCreate(
            // ['kode_barang' => $row['kode_barang']], // Kunci pencarian (agar tidak duplikat)
            [
                'kode_barang' => $row['kode_barang'],
                'nama_barang' => $row['nama_barang'],
                'merk_barang' => $row['merk_barang'],
                'keterangan'  => $row['keterangan'] ?? '-', // Default value jika kosong
                'lokasi'      => $row['lokasi'],
                'stok_barang' => $row['stok_barang'] ?? 0, // Pastikan header excelnya 'stok' atau sesuaikan
                'pagu'        => $row['pagu'] ?? 0,
                'harga_kulak' => $row['harga_kulak'],
                'harga_jual'  => $row['harga_jual'],
                'distributor' => $row['distributor'],
                'jenis'       => $row['jenis'],
            ]
        );
    }
}
