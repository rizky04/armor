<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceJob extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceJobFactory> */
    use HasFactory;

    protected $fillable = ['service_id', 'id_jasa', 'price', 'qty', 'subtotal'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

  public function jasa()
{
    return $this->belongsTo(Jasa::class, 'id_jasa', 'id_jasa');
}
}
