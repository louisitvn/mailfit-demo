<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUserGroupsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_groups', function (Blueprint $table) {
            $table->integer('custom_order')->default(0);
            $table->integer('user_id')->unsigned()->nullable();

            // foreign
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropColumn('custom_order');
            $table->dropForeign('user_groups_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
