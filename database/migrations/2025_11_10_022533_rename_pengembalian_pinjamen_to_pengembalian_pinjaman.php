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
        if (Schema::hasTable('pengembalian_pinjamen') && ! Schema::hasTable('pengembalian_pinjaman')) {
            Schema::rename('pengembalian_pinjamen', 'pengembalian_pinjaman');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengembalian_pinjaman') && !Schema::hasTable('pengembalian_pinjamen')) {
            Schema::rename('pengembalian_pinjaman', 'pengembalian_pinjamen');
        }
    }
};
