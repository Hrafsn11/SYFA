<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->enum('flagging_investor', ['sfinance', 'sfinlog'])
                ->nullable()
                ->after('flagging');
        });
    }

    public function down(): void
    {
        Schema::table('master_debitur_dan_investor', function (Blueprint $table) {
            $table->dropColumn('flagging_investor');
        });
    }
};
