<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOpnameLog extends Model
{
    use HasFactory;

    protected $table = 'stok_opname_logs';

    protected $fillable = [
        'id_barang',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'tanggal',
    ];

    public $timestamps = false;

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
