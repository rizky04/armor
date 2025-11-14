<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    //
    protected $table = 'tbl_penjualan';
    protected $primaryKey = 'id_penjualan';
    public $timestamps = false;
    protected $fillable = [
        'id_barang',
        'jumlah_penjualan',
        'harga_jual',
        'harga_kulak',
        'id_transaksi',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
