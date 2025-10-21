<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_invoice_financing', function (Blueprint $table) {
            $table->increments('id_invoice_financing');
            $table->unsignedInteger('id_debitur');
            $table->unsignedInteger('id_instansi')->nullable();
            $table->enum('sumber_pembiayaan', ['eksternal','internal'])->default('eksternal');
            $table->enum('nama_bank', ['BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'])->nullable();
            $table->string('no_rekening', 100)->nullable();
            $table->string('nama_rekening', 255)->nullable();
            $table->string('lampiran_sid', 255)->nullable();
            $table->string('tujuan_pembiayaan', 255)->nullable();
            $table->decimal('total_pinjaman', 15, 2)->default(0);
            $table->date('harapan_tanggal_pencairan')->nullable();
            $table->decimal('total_bagi_hasil', 15, 2)->default(0);
            $table->date('rencana_tgl_pembayaran')->nullable();
            $table->decimal('pembayaran_total', 15, 2)->default(0);
            $table->text('catatan_lainnya')->nullable();
            $table->string('status', 50)->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('id_debitur');
            $table->index('status');

            $table->foreign('id_debitur')->references('id_debitur')->on('master_debitur_dan_investor')->onUpdate('cascade')->onDelete('restrict');
            if (Schema::hasTable('master_sumber_pendanaan_eksternal')) {
                $table->foreign('id_instansi')->references('id_instansi')->on('master_sumber_pendanaan_eksternal')->onUpdate('cascade')->onDelete('set null');
            }
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_invoice_financing', function (Blueprint $table) {
            if (Schema::hasTable('peminjaman_invoice_financing')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
            }
        });
        Schema::dropIfExists('peminjaman_invoice_financing');
    }
};
