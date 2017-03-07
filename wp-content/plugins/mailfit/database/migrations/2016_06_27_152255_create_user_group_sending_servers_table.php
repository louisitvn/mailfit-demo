<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupSendingServersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_group_sending_servers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sending_server_id');
            $table->string('user_group_id');
            $table->integer('fitness');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('user_group_sending_servers');
    }
}
