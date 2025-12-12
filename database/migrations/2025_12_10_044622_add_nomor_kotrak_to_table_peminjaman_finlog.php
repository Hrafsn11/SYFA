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
        Schema::table('peminjaman_finlog', function (Blueprint $table) {
            $table->string('nomor_kontrak')->after('current_step')->nullable()->unique();
            $table->decimal('biaya_administrasi', 15, 2)->after('nomor_kontrak')->nullable();
            $table->text('jaminan')->after('biaya_administrasi')->nullable();
            $table->string('bukti_transfer')->after('jaminan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_finlog', function (Blueprint $table) {
            $table->dropColumn(['nomor_kontrak', 'biaya_administrasi', 'jaminan', 'bukti_transfer']);
        });
    }
};
