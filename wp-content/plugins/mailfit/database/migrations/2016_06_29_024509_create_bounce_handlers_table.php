<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBounceHandlersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bounce_handlers', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uid');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('host');
            $table->string('username');
            $table->string('password');
            $table->string('port');
            $table->string('protocol');
            $table->string('encryption');
            $table->string('status');
            $table->string('custom_order');

            $table->timestamps();

            // foreign
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('bounce_handlers');
    }
}
