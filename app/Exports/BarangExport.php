<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangExport implements FromCollection, WithHeadings
{
    public function collection()
    {
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

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Merk Barang',
            'Keterangan',
            'Lokasi',
            'Stok',
            'Pagu',
            'Harga Kulak',
            'Harga Jual',
            'Distributor',
            'Jenis',
        ];
    }
}
