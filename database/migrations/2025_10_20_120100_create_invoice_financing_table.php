<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_financing', function (Blueprint $table) {
            $table->increments('id_invoice');
            $table->unsignedInteger('id_peminjaman');
            $table->string('no_invoice', 255);
            $table->string('nama_client', 255)->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0);
            $table->decimal('nilai_pinjaman', 15, 2)->default(0);
            $table->decimal('nilai_bagi_hasil', 15, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('dokumen_invoice', 255)->nullable();
            $table->string('dokumen_kontrak', 255)->nullable();
            $table->string('dokumen_so', 255)->nullable();
            $table->string('dokumen_bast', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('id_peminjaman');
            $table->index('no_invoice');

            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman_invoice_financing')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_financing');
    }
};
