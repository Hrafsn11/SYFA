<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('factoring_details') && !Schema::hasTable('factoring_financing')) {
            Schema::rename('factoring_details', 'factoring_financing');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('factoring_financing') && !Schema::hasTable('factoring_details')) {
            Schema::rename('factoring_financing', 'factoring_details');
        }
    }
};
