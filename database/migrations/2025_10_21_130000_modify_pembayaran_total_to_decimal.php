<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `peminjaman_po_financing` MODIFY `pembayaran_total` DECIMAL(15,2) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `peminjaman_po_financing` MODIFY `pembayaran_total` FLOAT(8,2) NOT NULL");
    }
};
