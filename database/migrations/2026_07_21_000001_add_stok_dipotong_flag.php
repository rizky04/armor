<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualan_platform', function (Blueprint $table) {
            $table->boolean('stok_dipotong')->default(false)->after('status');
        });
        Schema::table('transaksi_motor', function (Blueprint $table) {
            $table->boolean('stok_dipotong')->default(false)->after('status');
        });

        // Backfill: aturan lama hanya memotong stok saat status 'selesai'
        DB::table('penjualan_platform')->where('status', 'selesai')->update(['stok_dipotong' => true]);
        DB::table('transaksi_motor')->where('status', 'selesai')->update(['stok_dipotong' => true]);
    }

    public function down(): void
    {
        Schema::table('penjualan_platform', fn (Blueprint $t) => $t->dropColumn('stok_dipotong'));
        Schema::table('transaksi_motor', fn (Blueprint $t) => $t->dropColumn('stok_dipotong'));
    }
};
