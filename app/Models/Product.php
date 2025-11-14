<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'tbl_barang';
    protected $primaryKey = 'id_barang'; // tambahkan ini
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'merk_barang',
        'keterangan',
        'lokasi',
        'stok_barang',
        'pagu',
        'harga_kulak',
        'harga_jual',
        'distributor',
        'jenis',
        'hapus',
    ];

    public $timestamps = false;

    public function serviceSpareparts()
    {
        return $this->hasMany(ServiceSparepart::class, 'id_barang', 'id_barang');
    }
}
