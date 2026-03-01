<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('services', function (Blueprint $table) {
        // Mengubah vehicle_id menjadi nullable (pastikan doctrine/dbal terinstall jika versi Laravel lama)
        $table->unsignedBigInteger('vehicle_id')->nullable()->change();

        // Menambahkan kolom manual setelah vehicle_id
        $table->string('manual_customer_name')->nullable()->after('vehicle_id');
        $table->string('manual_license_plate')->nullable()->after('manual_customer_name');
        $table->string('manual_vehicle_name')->nullable()->after('manual_license_plate');
    });
}

public function down()
{
    Schema::table('services', function (Blueprint $table) {
        $table->unsignedBigInteger('vehicle_id')->nullable(false)->change();
        $table->dropColumn(['manual_customer_name', 'manual_license_plate', 'manual_vehicle_name']);
    });
}
};
