<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'kode_barang',
            'nama_barang',
            'merk_barang',
            'keterangan',
            'lokasi',
            'stok',
            'pagu',
            'harga_kulak',
            'harga_jual',
            'distributor',
            'jenis',
        ];
    }
}
