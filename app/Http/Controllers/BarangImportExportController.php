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
        return Excel::download(new BarangExport, 'barang.xlsx');
    }

  public function import(Request $request)
{

    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);



    Excel::import(new BarangImport(), $request->file('file'));

    return back()->with('success', 'Import barang berhasil!');
}

}
