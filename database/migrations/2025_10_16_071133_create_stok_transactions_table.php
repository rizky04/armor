<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok_transactions', function (Blueprint $table) {
              $table->id();
             $table->integer('id_barang');
             $table->foreign('id_barang')->references('id_barang')->on('tbl_barang')->onDelete('cascade');
            $table->enum('jenis_transaksi', [
                'masuk', 'keluar', 'rusak', 'return_pembelian', 'return_penjualan', 'koreksi'
            ]);
            $table->integer('jumlah')->default(0);
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->string('keterangan')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_transactions');
    }
};
