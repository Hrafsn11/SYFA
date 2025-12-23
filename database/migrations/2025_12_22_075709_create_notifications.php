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
        Schema::create('notifications', function (Blueprint $table) {
            $table->ulid('id_notification')->primary();
            $table->string('type');
            $table->text('content');
            $table->string('link');
            $table->string('status');
            $table->string('status_hide')->default('unhide');
            $table->ulid('user_id');
            $table->timestamps();

            $table->foreign('user_id', 'fk_notification_to_user')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
