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
        Schema::create('peminjaman_installment_financing', function (Blueprint $table) {
            $table->id('id_installment');
            $table->unsignedBigInteger('id_debitur');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 100)->nullable();
            $table->string('nama_rekening')->nullable();
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->enum('tenor_pembayaran', ['3','6','9','12']);
            $table->decimal('persentase_bagi_hasil', 8, 4)->nullable();
            $table->decimal('pps', 15, 2)->nullable();
            $table->decimal('sfinance', 15, 2)->nullable();
            $table->decimal('total_pembayaran', 15, 2)->nullable();
            $table->string('status')->default('draft');
            $table->decimal('yang_harus_dibayarkan', 15, 2)->nullable();
            $table->string('catatan_lainnya')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_installment_financing');
    }
};
