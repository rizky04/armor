<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_platform', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_penjualan')->unique();
            $table->string('platform')->default('shopee'); // shopee, tokopedia, offline, dll
            $table->string('nomor_pesanan')->nullable();   // nomor order dari platform
            $table->string('nama_pembeli')->nullable();
            $table->date('tanggal');
            $table->text('catatan')->nullable();

            // Pendapatan
            $table->decimal('total_harga_jual', 15, 2)->default(0); // sum item harga_jual * qty
            $table->decimal('total_modal', 15, 2)->default(0);      // sum item harga_kulak * qty

            // Biaya-biaya
            $table->decimal('biaya_admin', 15, 2)->default(0);      // komisi / biaya platform
            $table->decimal('biaya_pengiriman', 15, 2)->default(0); // ongkir yg dibayar / dipotong
            $table->decimal('biaya_lainnya', 15, 2)->default(0);    // biaya penyesuaian, dll

            // Hasil akhir
            $table->decimal('penghasilan_bersih', 15, 2)->default(0); // total_jual - semua biaya
            $table->decimal('laba_bersih', 15, 2)->default(0);        // penghasilan - modal

            $table->enum('status', ['pending', 'selesai', 'dibatalkan'])->default('selesai');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('penjualan_platform_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penjualan_platform_id');
            $table->integer('barang_id');
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->decimal('harga_kulak', 15, 2)->default(0);  // dari master barang
            $table->decimal('harga_jual', 15, 2);               // input manual
            $table->integer('qty');
            $table->decimal('subtotal_modal', 15, 2)->default(0);
            $table->decimal('subtotal_jual', 15, 2);
            $table->timestamps();

            $table->foreign('penjualan_platform_id')
                  ->references('id')->on('penjualan_platform')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_platform_item');
        Schema::dropIfExists('penjualan_platform');
    }
};
