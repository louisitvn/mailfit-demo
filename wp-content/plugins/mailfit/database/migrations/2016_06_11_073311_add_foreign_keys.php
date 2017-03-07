<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });

        Schema::table('mail_lists', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_contact_id_foreign');
            $table->dropForeign('users_user_group_id_foreign');
            $table->dropForeign('users_language_id_foreign');
        });

        Schema::table('mail_lists', function (Blueprint $table) {
            $table->dropForeign('mail_lists_contact_id_foreign');
            $table->dropForeign('mail_lists_user_id_foreign');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign('contacts_country_id_foreign');
        });
    }
}
