<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PenjualanPlatform extends Model
{
    protected $table = 'penjualan_platform';

    protected $fillable = [
        'nomor_penjualan', 'platform', 'nomor_pesanan', 'nama_pembeli',
        'tanggal', 'catatan',
        'total_harga_jual', 'total_modal',
        'biaya_admin', 'biaya_pengiriman', 'biaya_lainnya',
        'penghasilan_bersih', 'laba_bersih',
        'status', 'stok_dipotong', 'created_by',
    ];

    public static $platformList = ['Shopee', 'Tokopedia', 'Lazada', 'TikTok Shop', 'Instagram', 'Offline', 'Lainnya'];

    public static function generateNomor(): string
    {
        return DB::transaction(function () {
            $prefix = 'PJL-' . date('Ymd') . '-';
            $last = self::where('nomor_penjualan', 'like', $prefix . '%')
                        ->lockForUpdate()
                        ->orderByDesc('nomor_penjualan')
                        ->first();
            $seq = $last ? ((int) substr($last->nomor_penjualan, -4)) + 1 : 1;
            return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        });
    }

    public function items()
    {
        return $this->hasMany(PenjualanPlatformItem::class, 'penjualan_platform_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
