<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    /** @use HasFactory<\Database\Factories\PromoFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'buy_count',
        'free_count',
        'is_active'
    ];
}
