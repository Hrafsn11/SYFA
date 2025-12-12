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
        Schema::create('projects', function (Blueprint $table) {
            $table->ulid('id_project')->primary();
            $table->ulid('id_cells_project'); 
            $table->string('nama_project')->nullable();
            $table->timestamps();


            $table->foreign('id_cells_project', 'fk_project_to_cells_project')
                ->references('id_cells_project')
                ->on('cells_projects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
