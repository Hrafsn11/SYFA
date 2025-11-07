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
        Schema::dropIfExists('peminjaman_invoice_financing');
        Schema::dropIfExists('invoice_financing');
        Schema::dropIfExists('peminjaman_po_financing');
        Schema::dropIfExists('po_financing');
        Schema::dropIfExists('peminjaman_installment_financing');
        Schema::dropIfExists('installment_financing');
        Schema::dropIfExists('peminjaman_factoring');
        Schema::dropIfExists('factoring_financing');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      
    }
};
