<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /** @use HasFactory<\Database\Factories\JobFactory> */
    use HasFactory;
    protected $table = 'tbl_jasa';
    protected $fillable = [
        'nama_jasa',
        'harga_jasa',
        'hapus',
    ];

    public $timestamps = false;
}
