<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            // Nomor Telepon
            $table->string('no_telepon', 20)->nullable()->after('email');
            
            // Status: active atau non active
            $table->enum('status', ['active', 'non active'])->default('active')->after('no_telepon');
            
            // Deposito: reguler atau khusus (khusus untuk investor)
            $table->enum('deposito', ['reguler', 'khusus'])->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->dropColumn(['no_telepon', 'status', 'deposito']);
        });
    }
};
