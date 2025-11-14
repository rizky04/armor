<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;
    protected $appends = ['total_paid', 'due_amount', 'omzet', 'profit'];
    protected $fillable = [
        'nomor_service',
        'vehicle_id',
        'created_by',
        'updated_by',
        'service_date',
        'estimate_date',
        'due_date',
        'category',
        'complaint',
        'status',
        'status_bayar',
        'total_cost',
        'id_transaksi',
    ];

    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function updater()
{
    return $this->belongsTo(User::class, 'updated_by');
}

public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanics()
    {
        return $this->belongsToMany(Mechanic::class, 'service_mechanics');
    }

    public function jobs()
    {
        return $this->hasMany(ServiceJob::class);
    }

    public function spareparts()
    {
        return $this->hasMany(ServiceSparepart::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function hutang(){
        return $this->belongsTo(Hutang::class, 'id_transaksi', 'id_transaksi');
    }

   //hutang
    public function payments()
{
    return $this->hasMany(ServicePayment::class);
}
public function getTotalPaidAttribute()
{
    return $this->payments()->sum('amount_paid');
}

public function getDueAmountAttribute()
{
    if ($this->total_paid > $this->total_cost ) {
        return 0;
    }else{
        return $this->total_cost - $this->total_paid;
    }

}

public function payment()
{
    return $this->hasOne(ServicePayment::class);
}


// $service->total_paid
// $service->due_amount


   //hutang


//     protected static function booted()
// {
//     static::creating(function ($service) {
//         $today = date('Ymd');
//         $prefix = 'SRV-' . $today . '-';

//         // Ambil nomor terakhir hari ini
//         $lastService = self::where('nomor_service', 'like', $prefix . '%')
//             ->orderBy('nomor_service', 'desc')
//             ->first();

//         if ($lastService) {
//             $lastNumber = (int) substr($lastService->nomor_service, -3);
//             $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
//         } else {
//             $newNumber = '001';
//         }

//         $service->nomor_service = $prefix . $newNumber;
//     });
// }

protected static function booted()
{
    static::creating(function ($service) {
        DB::transaction(function () use ($service) {
            $today = date('Ymd');
            $prefix = 'SRV-' . $today . '-';

            // Kunci baris terkait supaya tidak bentrok antar transaksi
            $lastService = self::where('nomor_service', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('nomor_service', 'desc')
                ->first();

            if ($lastService) {
                $lastNumber = (int) substr($lastService->nomor_service, -3);
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }

            $service->nomor_service = $prefix . $newNumber;
        });
    });
}

// public function scopeBetweenDates($query, $start, $end)
// {
//     return $query->whereBetween('service_date', [$start, $end]);
// }

public function scopeBetweenDates($query, $start, $end)
{
    return $query->whereBetween(DB::raw('DATE(service_date)'), [$start, $end]);
}

// Omzet (total jual jasa + barang)
public function getOmzetAttribute()
{
    $jasa = $this->jobs->sum(fn($job) => $job->price * $job->qty);
    $barang = $this->spareparts->sum(fn($sp) => $sp->price * $sp->qty);
    return $jasa + $barang;
}

// Profit (selisih jual - beli barang + jasa)
public function getProfitAttribute()
{
    $profit_barang = $this->spareparts->sum(fn($sp) => ($sp->price - $sp->purchase_price) * $sp->qty);
    $profit_jasa = $this->jobs->sum(fn($job) => $job->price * $job->qty);
    return $profit_barang + $profit_jasa;
}


public function oilServices()
{
    return $this->hasMany(OilService::class);
}


}
