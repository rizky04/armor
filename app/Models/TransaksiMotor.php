<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransaksiMotor extends Model
{
    protected $table = 'transaksi_motor';

    protected $fillable = [
        'nomor_transaksi',
        'nama_customer',
        'no_hp',
        'plat_nomor',
        'nama_motor',
        'tanggal',
        'catatan',
        'status',
        'stok_dipotong',
        'metode_pembayaran',
        'total_barang',
        'total_jasa',
        'diskon',
        'total',
        'created_by',
    ];

    public static function generateNomor(): string
    {
        return DB::transaction(function () {
            $prefix = 'TRX-' . date('Ymd') . '-';
            $last = self::where('nomor_transaksi', 'like', $prefix . '%')
                        ->lockForUpdate()
                        ->orderByDesc('nomor_transaksi')
                        ->first();

            $seq = $last ? ((int) substr($last->nomor_transaksi, -4)) + 1 : 1;
            return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        });
    }

    public function barangs()
    {
        return $this->hasMany(TransaksiMotorBarang::class, 'transaksi_motor_id');
    }

    public function jasas()
    {
        return $this->hasMany(TransaksiMotorJasa::class, 'transaksi_motor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
