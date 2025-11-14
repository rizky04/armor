<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSparepart extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceSparepartFactory> */
    use HasFactory;

    protected $fillable = ['service_id', 'id_barang', 'price', 'purchase_price', 'qty', 'subtotal'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // public function sparepart()
    // {
    //     return $this->belongsTo(Product::class, 'id_barang');
    // }
}
