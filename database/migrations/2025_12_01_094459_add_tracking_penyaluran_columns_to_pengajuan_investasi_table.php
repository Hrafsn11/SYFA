<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->decimal('total_disalurkan', 15, 2)->default(0)->after('sisa_bagi_hasil');
            
            $table->decimal('total_kembali_dari_penyaluran', 15, 2)->default(0)->after('total_disalurkan');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_investasi', function (Blueprint $table) {
            $table->dropColumn(['total_disalurkan', 'total_kembali_dari_penyaluran']);
        });
    }
};
