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
        Schema::create('history_pengajuan_pinjaman_finlog', function (Blueprint $table) {
            $table->ulid('id_history_pengajuan_pinjaman_finlog')->primary();

            // Foreign key ke pengajuan pinjaman finlog
            $table->ulid('id_peminjaman_finlog');

            // User tracking
            $table->ulid('submit_step1_by')->nullable();
            $table->ulid('reject_by')->nullable();
            $table->ulid('approve_by')->nullable();

            // DateTime tracking
            $table->date('date')->nullable();
            $table->time('time')->nullable();

            // Data fields
            $table->decimal('bagi_hasil_disetujui', 5, 2)->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->string('status')->nullable();
            $table->integer('current_step')->default(1);
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_peminjaman_finlog')->references('id_peminjaman_finlog')->on('peminjaman_finlog')->onDelete('cascade');
            $table->foreign('submit_step1_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reject_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approve_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_pengajuan_pinjaman_finlog');
    }
};
