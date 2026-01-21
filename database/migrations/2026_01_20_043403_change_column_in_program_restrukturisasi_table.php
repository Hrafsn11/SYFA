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
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->enum('metode_perhitungan', ['Flat', 'Anuitas'])->nullable()->default('Flat')->change();
            $table->double('plafon_pembiayaan', 15, 2)->nullable()->change();
            $table->double('suku_bunga_per_tahun', 5, 2)->nullable()->change();
            $table->integer('jangka_waktu_total')->nullable()->change();
            $table->integer('masa_tenggang')->nullable()->change();
            $table->date('tanggal_mulai_cicilan')->nullable()->change();
            $table->double('total_pokok', 15, 2)->nullable()->default(0.00)->change();
            $table->double('total_margin', 15, 2)->nullable()->default(0.00)->change();
            $table->double('total_cicilan', 15, 2)->nullable()->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_restrukturisasi', function (Blueprint $table) {
            $table->enum('metode_perhitungan', ['Flat', 'Anuitas'])->nullable(false)->default('Flat')->change();
            $table->double('plafon_pembiayaan', 15, 2)->nullable(false)->change();
            $table->double('suku_bunga_per_tahun', 5, 2)->nullable(false)->change();
            $table->integer('jangka_waktu_total')->nullable(false)->change();
            $table->integer('masa_tenggang')->nullable(false)->change();
            $table->date('tanggal_mulai_cicilan')->nullable(false)->change();
            $table->double('total_pokok', 15, 2)->nullable(false)->default(0.00)->change();
            $table->double('total_margin', 15, 2)->nullable(false)->default(0.00)->change();
            $table->double('total_cicilan', 15, 2)->nullable(false)->default(0.00)->change();
        });
    }
};
