<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';

    protected $fillable = [
        'tanggal',
        'kategori',
        'keterangan',
        'jumlah',
        'catatan',
        'created_by',
    ];

    public static $kategoriList = [
        'Gaji Karyawan',
        'Listrik',
        'Air',
        'Sewa Tempat',
        'Peralatan',
        'Transportasi',
        'Operasional',
        'Lain-lain',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
