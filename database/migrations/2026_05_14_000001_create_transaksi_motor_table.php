<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_motor', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->string('nama_customer');
            $table->string('no_hp')->nullable();
            $table->string('plat_nomor')->nullable();
            $table->string('nama_motor')->nullable();
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->decimal('total_barang', 15, 2)->default(0);
            $table->decimal('total_jasa', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_motor');
    }
};
