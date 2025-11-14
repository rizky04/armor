<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    //
    protected $table = 'tbl_transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;
    protected $fillable = [
        'tgl_transaksi',
        'id_pengguna',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_transaksi', 'id_transaksi');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class, 'id_transaksi', 'id_transaksi');
    }

    public function salesItem()
    {
        return $this->hasMany(SalesItem::class, 'id_transaksi', 'id_transaksi');
    }




}
