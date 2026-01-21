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
        Schema::table('jadwal_angsuran', function (Blueprint $table) {
            $table->date('tanggal_jatuh_tempo')->nullable()->change();
            $table->double('pokok', 15, 2)->nullable()->change();
            $table->double('margin', 15, 2)->nullable()->change();
            $table->double('total_cicilan', 15, 2)->nullable()->change();
            $table->tinyInteger('is_grace_period')->nullable()->change();
            $table->enum('status', ['Belum Jatuh Tempo','Jatuh Tempo','Lunas','Tertunda'])
                ->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_angsuran', function (Blueprint $table) {
            $table->date('tanggal_jatuh_tempo')->nullable(false)->change();
            $table->double('pokok', 15, 2)->nullable(false)->change();
            $table->double('margin', 15, 2)->nullable(false)->change();
            $table->double('total_cicilan', 15, 2)->nullable(false)->change();
            $table->tinyInteger('is_grace_period')->nullable(false)->change();
            $table->enum('status', ['Belum Jatuh Tempo','Jatuh Tempo','Lunas','Tertunda'])
                ->nullable(false)->change();
        });
    }
};
