<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OilService extends Model
{
    protected $fillable = [
        'service_id',
        'service_date',
        'km_service',
        'km_service_next',
        'next_service_date',
        'oil_name',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
