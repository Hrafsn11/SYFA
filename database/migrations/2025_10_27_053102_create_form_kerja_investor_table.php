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
        Schema::create('form_kerja_investor', function (Blueprint $table) {
            $table->id('id_form_kerja_investor');
            $table->unsignedInteger('id_debitur'); // Match with master_debitur_dan_investor
            $table->string('nama_investor');
            $table->enum('deposito', ['reguler', 'khusus']);
            $table->date('tanggal_pembayaran')->nullable();
            $table->integer('lama_investasi')->nullable()->comment('Dalam bulan');
            $table->decimal('jumlah_investasi', 15, 2);
            $table->decimal('bagi_hasil', 5, 2)->comment('Persentase bagi hasil');
            $table->decimal('bagi_hasil_keseluruhan', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('alasan_penolakan')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->text('keterangan_bukti')->nullable();
            $table->string('nomor_kontrak')->nullable();
            $table->date('tanggal_kontrak')->nullable();
            $table->text('catatan_kontrak')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('id_debitur')
                ->references('id_debitur')
                ->on('master_debitur_dan_investor')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_kerja_investor');
    }
};
