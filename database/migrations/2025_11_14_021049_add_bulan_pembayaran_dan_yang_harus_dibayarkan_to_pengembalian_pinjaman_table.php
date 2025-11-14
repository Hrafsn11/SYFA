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
        Schema::table('pengembalian_pinjaman', function (Blueprint $table) {
            $table->string('bulan_pembayaran', 20)->nullable()->after('invoice_dibayarkan');
            $table->decimal('yang_harus_dibayarkan', 15, 2)->nullable()->after('bulan_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian_pinjaman', function (Blueprint $table) {
            $table->dropColumn(['bulan_pembayaran', 'yang_harus_dibayarkan']);
        });
    }
};
