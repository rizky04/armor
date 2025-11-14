<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePayment extends Model
{
    /** @use HasFactory<\Database\Factories\ServicePaymentFactory> */
    use HasFactory;
     protected $fillable = [
        'service_id',
        'amount_paid',
        'change_amount',
        'payment_type',
        'reference',
        'note',
        'payment_date',
        'created_by',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
