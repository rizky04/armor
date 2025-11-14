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
        Schema::create('sales', function (Blueprint $table) {
        $table->id();

        // ikuti tipe kolom lama: INT
        $table->integer('id_client');
        $table->foreign('id_client')->references('id_client')->on('tbl_client')->onDelete('cascade');

        // ini tabel lama juga, mungkin INT
        $table->integer('id_transaksi');
        $table->foreign('id_transaksi')->references('id_transaksi')->on('tbl_transaksi')->onDelete('cascade');

        $table->unsignedBigInteger('id_user');
        $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

        $table->string('nomor_sales')->unique();
        $table->date('sales_date');
        $table->date('due_date')->nullable();
        $table->enum('status_bayar', ['belum bayar', 'hutang', 'lunas'])->default('belum bayar');
        $table->decimal('total', 15, 2);
        $table->text('note')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
