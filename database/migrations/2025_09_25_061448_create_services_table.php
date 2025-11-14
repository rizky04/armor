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
        Schema::create('services', function (Blueprint $table) {
           $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->dateTime('service_date');
            $table->enum('category', ['fast service', 'inap']);
            $table->text('complaint')->nullable();
            $table->enum('status', ['menunggu','proses','selesai','diambil'])->default('menunggu');
            $table->dateTime('service_progress')->nullable();
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
