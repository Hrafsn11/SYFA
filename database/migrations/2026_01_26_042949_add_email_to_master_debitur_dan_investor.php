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
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->string('email_ceo')->nullable()->after('nama_ceo');
            $table->string('email_direktur_holding')->nullable()->after('nama_direktur_holding');
            $table->string('email_komisaris')->nullable()->after('nama_komisaris');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->dropColumn(['email_ceo', 'email_direktur_holding', 'email_komisaris']);
        });
    }
};
