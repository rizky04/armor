<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BarangExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * Mengambil data dari database
    */
    public function collection()
    {
        // Mengambil semua data, atau Anda bisa memfilter kolom tertentu
        return Barang::select(
            'kode_barang',
            'nama_barang',
            'merk_barang',
            'keterangan',
            'lokasi',
            'stok_barang',
            'pagu',
            'harga_kulak',
            'harga_jual',
            'distributor',
            'jenis'
        )->get();
    }

    /**
    * Menentukan Judul Kolom (Header) di Excel
    */
    public function headings(): array
    {
        return [
           'kode_barang',
            'nama_barang',
            'merk_barang',
            'keterangan',
            'lokasi',
            'stok_barang',
            'pagu',
            'harga_kulak',
            'harga_jual',
            'distributor',
            'jenis'
        ];
    }
}
