<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_motor_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_motor_id');
            $table->integer('barang_id');
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->decimal('harga', 15, 2);
            $table->integer('qty');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->foreign('transaksi_motor_id')
                  ->references('id')->on('transaksi_motor')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_motor_barang');
    }
};
