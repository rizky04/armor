<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiMotorJasa extends Model
{
    protected $table = 'transaksi_motor_jasa';

    protected $fillable = [
        'transaksi_motor_id',
        'jasa_id',
        'kode_jasa',
        'nama_jasa',
        'harga',
        'qty',
        'subtotal',
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiMotor::class, 'transaksi_motor_id');
    }

    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'jasa_id', 'id_jasa');
    }
}
