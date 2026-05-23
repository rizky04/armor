<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_motor_jasa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_motor_id');
            $table->integer('jasa_id');
            $table->string('kode_jasa')->nullable();
            $table->string('nama_jasa');
            $table->decimal('harga', 15, 2);
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->foreign('transaksi_motor_id')
                  ->references('id')->on('transaksi_motor')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_motor_jasa');
    }
};
