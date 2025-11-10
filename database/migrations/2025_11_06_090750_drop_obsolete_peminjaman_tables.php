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
        // Disable foreign key checks temporarily
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('peminjaman_invoice_financing');
        Schema::dropIfExists('invoice_financing');
        Schema::dropIfExists('peminjaman_po_financing');
        Schema::dropIfExists('po_financing');
        Schema::dropIfExists('peminjaman_installment_financing');
        Schema::dropIfExists('installment_financing');
        Schema::dropIfExists('peminjaman_factoring');
        Schema::dropIfExists('factoring_financing');
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      
    }
};
