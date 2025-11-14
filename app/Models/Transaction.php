<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
    protected $fillable = [
        'reference', 'customer_id', 'created_by', 'updated_by', 'total', 'discount', 'tax', 'total_after_tax', 'cash', 'payment_method', 'change',  'plate_photo' ,'date'
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

  public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function updatedBy()
{
    return $this->belongsTo(User::class, 'updated_by');
}


    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->reference = 'TEMP';
        });

        static::created(function ($transaction) {
            $today = now()->format('Ymd');
            $transaction->reference = 'TRX-' . $today . '-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
            $transaction->save();
        });
        // static::creating(function ($transaction) {
        //     $today = now()->format('Ymd'); // contoh: 20250905

        //     // Hitung jumlah transaksi hari ini
        //     $countToday = DB::table('transactions')
        //         ->whereDate('created_at', now()->toDateString())
        //         ->count();

        //     // Nomor urut transaksi (increment per hari)
        //     $nextNumber = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        //     // Generate reference
        //     $transaction->reference = 'TRX-' . $today . '-' . $nextNumber;
        // });
    }
}
