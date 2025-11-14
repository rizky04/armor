<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    /** @use HasFactory<\Database\Factories\PembelianFactory> */
    use HasFactory;
    protected $table = 'tbl_pembelian';
    protected $primaryKey = 'id_pembelian';
    protected $fillable = [
        'tgl_pembelian',
        'id_barang',
        'jumlah_pembelian',
        'harga_kulak',
        'harga_jual',
        'id_pengguna',
    ];
    public $timestamps = false;

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
