<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_motor_barang', function (Blueprint $table) {
            $table->decimal('harga_kulak', 15, 2)->default(0)->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_motor_barang', function (Blueprint $table) {
            $table->dropColumn('harga_kulak');
        });
    }
};
