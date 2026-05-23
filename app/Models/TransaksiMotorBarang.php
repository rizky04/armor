<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiMotorBarang extends Model
{
    protected $table = 'transaksi_motor_barang';

    protected $fillable = [
        'transaksi_motor_id',
        'barang_id',
        'kode_barang',
        'nama_barang',
        'harga',
        'harga_kulak',
        'qty',
        'subtotal',
    ];

    public function getKeuntunganAttribute(): float
    {
        return ($this->harga - $this->harga_kulak) * $this->qty;
    }

    public function transaksi()
    {
        return $this->belongsTo(TransaksiMotor::class, 'transaksi_motor_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id_barang');
    }
}
