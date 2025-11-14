<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    /** @use HasFactory<\Database\Factories\SalesItemFactory> */
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'id_transaksi',
        'id_barang',
        'price',
        'purchase_price',
        'qty',
        'subtotal'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    // public function sales()
    // {
    //     return $this->belongsTo(Sales::class);
    // }

    public function sales()
{
    return $this->belongsTo(Sales::class, 'id_sales', 'id');
}

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
