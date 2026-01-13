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
        Schema::table('history_status_pengajuan_investor', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('catatan_validasi_dokumen_ditolak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_status_pengajuan_investor', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
