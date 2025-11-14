<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Sales extends Model
{
    /** @use HasFactory<\Database\Factories\SalesFactory> */
    use HasFactory;
protected $appends = ['total_paid', 'due_amount'];
    protected $fillable = [
        'id_client',
        'id_transaksi',
        'id_user',
        'nomor_sales',
        'status_bayar',
        'sales_date',
        'due_date',
        'total',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
    public function items()
    {
        return $this->hasMany(SalesItem::class, 'sales_id', 'id');
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class, 'sales_id', 'id');
    }


    protected static function booted()
    {
        static::creating(function ($sales) {
            DB::transaction(function () use ($sales) {
                $today = date('Ymd');
                $prefix = 'PJL-' . $today . '-';

                // Kunci baris terkait supaya tidak bentrok antar transaksi
                $lastSales = self::where('nomor_sales', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderBy('nomor_sales', 'desc')
                    ->first();

                if ($lastSales) {
                    $lastNumber = (int) substr($lastSales->nomor_sales, -3);
                    $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '001';
                }

                $sales->nomor_sales = $prefix . $newNumber;
            });
        });
    }

public function payments()
{
    return $this->hasMany(SalesPayment::class, 'id_sales', 'id');
}
public function payment()
{
   return $this->hasOne(SalesPayment::class, 'id_sales', 'id')->latest('id');
}

public function getTotalPaidAttribute()
{
    return $this->payments()->sum('amount_paid');
}

public function getDueAmountAttribute()
{
    if ($this->total_paid > $this->total ) {
        return 0;
    }else{
        return $this->total - $this->total_paid;
    }
}


}
