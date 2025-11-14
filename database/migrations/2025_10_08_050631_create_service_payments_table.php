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
        Schema::create('service_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2)->default(0); // jumlah dibayar
            $table->string('payment_type')->default('cash');   // cash, transfer, dll
            $table->string('reference')->nullable();           // no referensi/invoice
            $table->text('note')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_payments');
    }
};
