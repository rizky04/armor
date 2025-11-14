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
        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_id');
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');

            $table->integer('id_transaksi'); // karena tabel lama pakai INT
            $table->foreign('id_transaksi')->references('id_transaksi')->on('tbl_transaksi')->onDelete('cascade');


             // relasi ke tabel barang
        $table->integer('id_barang');
        $table->foreign('id_barang')->references('id_barang')->on('tbl_barang')->onDelete('cascade');

        // harga jual & kulak disimpan supaya histori harga tidak berubah
        $table->decimal('price', 15, 2);
        $table->decimal('purchase_price', 15, 2)->nullable();
        $table->integer('qty');
        $table->decimal('subtotal', 15, 2); // harga_jual * qty

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_items');
    }
};
