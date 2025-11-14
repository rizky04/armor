<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengguna extends Model
{
     /** @use HasFactory<\Database\Factories\JasaFactory> */
     use HasFactory;
     protected $table = 'tbl_pengguna';
     protected $primaryKey = 'id_pengguna'; // tambahkan ini
     public $incrementing = true;
     protected $keyType = 'int';

     protected $fillable = [
         'nama',
         'password',
         'rule',
         'hapus',
     ];

     // Kalau di tabel `tbl_jasa` tidak ada created_at & updated_at
     public $timestamps = false;


     /**
      * Get the user that owns the Pengguna
      *
      * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
      */
     public function user(): BelongsTo
     {
         return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
     }

}
