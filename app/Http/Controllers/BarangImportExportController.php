<?php

namespace App\Http\Controllers;

use App\Exports\BarangExport;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class BarangImportExportController extends Controller
{
      public function export()
    {
        return Excel::download(new BarangExport, 'data_barang.xlsx');
    }


 public function import(Request $request)
    {
        // Validasi file harus ada dan formatnya xlsx/xls
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Proses import
            Excel::import(new BarangImport, $request->file('file'));

            return back()->with('success', 'Data Barang berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
