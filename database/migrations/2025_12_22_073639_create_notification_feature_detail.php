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
        Schema::create('notification_feature_detail', function (Blueprint $table) {
            $table->ulid('id_notification_feature_detail')->primary();
            $table->ulid('notification_feature_id');
            $table->longText('role_assigned');
            $table->text('message');
            $table->timestamps();

            $table->foreign('notification_feature_id', 'fk_notification_feature_detail_to_notification_feature')
                ->references('id_notification_feature')
                ->on('notification_feature')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_feature_detail');
    }
};
