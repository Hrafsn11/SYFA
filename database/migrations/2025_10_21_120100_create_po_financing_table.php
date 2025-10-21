<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_financing', function (Blueprint $table) {
            $table->increments('id_po_financing_detail');
            $table->unsignedInteger('id_po_financing');
            $table->string('no_kontrak', 255);
            $table->string('nama_client', 255)->nullable();
            $table->decimal('nilai_invoice', 15, 2);
            $table->decimal('nilai_pinjaman', 15, 2);
            $table->decimal('nilai_bagi_hasil', 15, 2);
            $table->date('kontrak_date');
            $table->date('due_date');
            $table->string('dokumen_kontrak', 255)->nullable();
            $table->string('dokumen_so', 255)->nullable();
            $table->string('dokumen_bast', 255)->nullable();
            $table->string('dokumen_lainnya', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('id_po_financing');
            $table->index('no_kontrak');
            $table->foreign('id_po_financing')->references('id_po_financing')->on('peminjaman_po_financing')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_financing');
    }
};
