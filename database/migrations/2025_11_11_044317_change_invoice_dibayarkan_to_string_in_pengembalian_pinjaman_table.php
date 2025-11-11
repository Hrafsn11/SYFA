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
            $table->string('invoice_dibayarkan', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian_pinjaman', function (Blueprint $table) {
            $table->enum('invoice_dibayarkan', ['iya', 'tidak'])->change();
        });
    }
};
