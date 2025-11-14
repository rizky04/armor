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
        Schema::create('oil_services', function (Blueprint $table) {
          $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->date('service_date'); // diambil dari service
            $table->integer('km_service')->nullable();
            $table->integer('km_service_next')->nullable();
            $table->date('next_service_date')->nullable();
            $table->string('oil_name'); // oli yang digunakan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oil_services');
    }
};
