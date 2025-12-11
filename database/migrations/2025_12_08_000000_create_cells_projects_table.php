<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cells_projects', function (Blueprint $table) {
            $table->ulid('id_cells_project')->primary();
            $table->string('nama_project');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cells_projects');
    }
};
