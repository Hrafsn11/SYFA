<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::getIndexListing() returns index names without needing doctrine/dbal.
        if (!in_array('pengajuan_peminjaman_id_instansi_index', Schema::getIndexListing('pengajuan_peminjaman'))) {
            Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
                $table->index('id_instansi');
            });
        }

        if (!in_array('bukti_peminjaman_id_pengajuan_peminjaman_index', Schema::getIndexListing('bukti_peminjaman'))) {
            Schema::table('bukti_peminjaman', function (Blueprint $table) {
                $table->index('id_pengajuan_peminjaman');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengajuan_peminjaman', function (Blueprint $table) {
            $table->dropIndex(['id_instansi']);
        });

        Schema::table('bukti_peminjaman', function (Blueprint $table) {
            $table->dropIndex(['id_pengajuan_peminjaman']);
        });
    }
};
