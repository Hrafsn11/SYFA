<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->decimal('nominal_pengajuan_awal', 15, 2)->nullable()->after('total_pinjaman');
        });

        DB::statement('UPDATE pengajuan_peminjaman SET nominal_pengajuan_awal = total_pinjaman WHERE nominal_pengajuan_awal IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->dropColumn('nominal_pengajuan_awal');
        });
    }
};
