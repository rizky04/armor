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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('total', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2);
            $table->decimal('total_after_tax', 15, 2);
            $table->decimal('cash', 15, 2);
            $table->decimal('change', 15, 2);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
