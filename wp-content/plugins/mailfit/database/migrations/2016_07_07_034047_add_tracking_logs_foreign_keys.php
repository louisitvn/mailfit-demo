<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrackingLogsForeignKeys extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tracking_logs', function (Blueprint $table) {
            // foreign
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sending_server_id')->references('id')->on('sending_servers')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tracking_logs', function (Blueprint $table) {
            $table->dropForeign('tracking_logs_user_id_foreign');
            $table->dropForeign('tracking_logs_sending_server_id_foreign');
            $table->dropForeign('tracking_logs_campaign_id_foreign');
            $table->dropForeign('tracking_logs_subscriber_id_foreign');
        });
    }
}
