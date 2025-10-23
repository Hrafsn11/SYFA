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
        Schema::create('installment_financing', function (Blueprint $table) {
            $table->id('id_installment_detail');
            $table->unsignedBigInteger('id_installment');
            $table->string('no_invoice');
            $table->string('nama_client')->nullable();
            $table->decimal('nilai_invoice', 15, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('dokumen_invoice')->nullable();
            $table->string('dokumen_lainnya')->nullable();
            $table->timestamps();

            $table->index('id_installment');
            $table->index('no_invoice');

            $table->foreign('id_installment')
                ->references('id_installment')
                ->on('peminjaman_installment_financing')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_financing');
    }
};
