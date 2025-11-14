<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jasa extends Model
{
    /** @use HasFactory<\Database\Factories\JasaFactory> */
    use HasFactory;
    protected $table = 'tbl_jasa';
    protected $primaryKey = 'id_jasa'; // tambahkan ini
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_jasa',
        'harga_jasa',
        'jenis',
        'hapus',
    ];

    // Kalau di tabel `tbl_jasa` tidak ada created_at & updated_at
    public $timestamps = false;

    public function serviceJobs()
    {
        return $this->hasMany(ServiceJob::class, 'id_jasa', 'id_jasa');
    }
}
