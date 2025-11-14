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
        Schema::create('report_pengembalian', function (Blueprint $table) {
            $table->ulid("id_report_pengembalian")->primary()->unique();
            $table->string("id_pengembalian", 26);
            $table->foreign("id_pengembalian")->references("ulid")->on("pengembalian_pinjaman")->onDelete("cascade");

            $table->string('nomor_peminjaman', 255);
            $table->string('nomor_invoice', 255);
            $table->date('due_date')->nullable();
            $table->string('hari_keterlambatan', 100);
            $table->string('total_bulan_pemakaian', 100);
            $table->decimal('nilai_total_pengembalian', 15, 2);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_pengembalian');
    }
};
