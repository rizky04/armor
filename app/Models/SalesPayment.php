<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    /** @use HasFactory<\Database\Factories\SalesPaymentFactory> */
    use HasFactory;
     protected $fillable = [
        'id_sales',
        'amount_paid',
        'change_amount',
        'payment_type',
        'reference',
        'note',
        'payment_date',
        'created_by',
    ];


     public function sales()
    {
        return $this->belongsTo(Sales::class, 'id_sales', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
