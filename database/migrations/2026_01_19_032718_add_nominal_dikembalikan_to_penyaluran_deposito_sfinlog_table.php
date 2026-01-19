<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            $table->decimal('nominal_yang_dikembalikan', 20, 2)->default(0)->after('nominal_yang_disalurkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyaluran_deposito_sfinlog', function (Blueprint $table) {
            $table->dropColumn('nominal_yang_dikembalikan');
        });
    }
};
