<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            if (Schema::hasColumn('master_debitur_dan_investor', 'id_kol')) {
                try {
                    $table->dropForeign(['id_kol']);
                } catch (\Exception $e) {
                    // No foreign key constraint, continue
                }
                $table->dropColumn('id_kol');
            }
        });
    }

    public function down(): void
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->string('id_kol')->nullable()->after('user_id');
        });
    }
};
