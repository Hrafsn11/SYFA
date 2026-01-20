<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            if (Schema::hasColumn('penyaluran_deposito_sfinlog', 'bukti_pengembalian')) {
                $table->dropColumn('bukti_pengembalian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            $table->string('bukti_pengembalian')->nullable()->after('tanggal_pengembalian');
        });
    }
};
