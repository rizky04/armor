<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanPlatformItem extends Model
{
    protected $table = 'penjualan_platform_item';

    protected $fillable = [
        'penjualan_platform_id', 'barang_id', 'kode_barang', 'nama_barang',
        'harga_kulak', 'harga_jual', 'qty', 'subtotal_modal', 'subtotal_jual',
    ];

    public function penjualan()
    {
        return $this->belongsTo(PenjualanPlatform::class, 'penjualan_platform_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id_barang');
    }
}
