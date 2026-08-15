<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_motor', function (Blueprint $table) {
            $table->decimal('diskon', 15, 2)->default(0)->after('total_jasa');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_motor', fn (Blueprint $t) => $t->dropColumn('diskon'));
    }
};
