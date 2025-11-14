<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMechanic extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceMechanicFactory> */
    use HasFactory;
      protected $table = 'service_mechanics';
    protected $fillable = ['service_id', 'mechanic_id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }
}
