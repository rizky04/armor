<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokTransaction extends Model
{
    use HasFactory;

    protected $table = 'stok_transactions';
    protected $fillable = [
        'id_barang',
        'jenis_transaksi',
        'jumlah',
        'stok_awal',
        'stok_akhir',
        'keterangan',
        'created_by',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
