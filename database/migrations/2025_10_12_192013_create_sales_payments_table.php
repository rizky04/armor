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
        Schema::create('sales_payments', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('id_sales');
            $table->foreign('id_sales')->references('id')->on('sales')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('change_amount', 15, 2);
            $table->string('payment_type')->default('cash');
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->date('payment_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_payments');
    }
};
