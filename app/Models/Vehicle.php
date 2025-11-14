<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;
    protected $fillable = [
        'id_client',
        'license_plate',
        'brand',
        'type',
        'engine_number',
        'chassis_number',
        'photo',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

  public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
