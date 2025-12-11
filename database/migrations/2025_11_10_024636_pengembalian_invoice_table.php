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
        Schema::create('pengembalian_invoice', function (Blueprint $table) {
            $table->id('id_pengembalian_invoice');
            $table->string('id_pengembalian', 26);
            $table->foreign('id_pengembalian')
                  ->references('ulid')
                  ->on('pengembalian_pinjaman')
                  ->onDelete('cascade');
            $table->decimal('nominal_yg_dibayarkan', 15, 2)->default(0);
            $table->string('bukti_pembayaran')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_invoice');
    }
};
