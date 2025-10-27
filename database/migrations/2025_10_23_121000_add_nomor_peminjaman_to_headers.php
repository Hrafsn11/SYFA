<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peminjaman_invoice_financing', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_invoice_financing')->index();
        });

        Schema::table('peminjaman_po_financing', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_po_financing')->index();
        });

        Schema::table('peminjaman_installment_financing', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_installment')->index();
        });

        Schema::table('peminjaman_factoring', function (Blueprint $table) {
            $table->string('nomor_peminjaman')->nullable()->after('id_factoring')->index();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_invoice_financing', function (Blueprint $table) {
            $table->dropColumn('nomor_peminjaman');
        });
        Schema::table('peminjaman_po_financing', function (Blueprint $table) {
            $table->dropColumn('nomor_peminjaman');
        });
        Schema::table('peminjaman_installment_financing', function (Blueprint $table) {
            $table->dropColumn('nomor_peminjaman');
        });
        Schema::table('peminjaman_factoring', function (Blueprint $table) {
            $table->dropColumn('nomor_peminjaman');
        });
    }
};
