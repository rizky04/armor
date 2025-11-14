<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    protected $table = 'tbl_piutang';
    protected $primaryKey = 'id_piutang';
    public $timestamps = false;
    protected $fillable = [
        'id_transaksi',
        'tgl_jatuh_tempo',
        'status_piutang',
        'id_client',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }
}
