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
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->integer('lama_pemakaian')->nullable()->after('last_penalty_calculation');
            $table->timestamp('last_lama_pemakaian_update')->nullable()->after('lama_pemakaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->dropColumn(['lama_pemakaian', 'last_lama_pemakaian_update']);
        });
    }
};
