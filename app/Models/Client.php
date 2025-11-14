<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;


    protected $table = 'tbl_client';
    protected $primaryKey = 'id_client'; // tambahkan ini
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'nama_client',
        'no_telp',
        'no_ktp',
        'alamat',
        'hapus',
    ];

    public $timestamps = false;

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'id_client', 'id_client');
    }

    public function service()
    {
        return $this->hasMany(Service::class, 'id_client', 'id_client');
    }

    public function hutang()
    {
        return $this->hasMany(Hutang::class, 'id_client', 'id_client');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class, 'id_client', 'id_client');
    }
}
