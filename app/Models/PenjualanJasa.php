<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanJasa extends Model
{
    //
    protected $table = 'tbl_penjualan_jasa';
    protected $primaryKey = 'id_penjualan_jasa';
    public $timestamps = false;
    protected $fillable = [
        'id_jasa',
        'id_transaksi',
    ];

    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa', 'id_jasa');
    }
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
