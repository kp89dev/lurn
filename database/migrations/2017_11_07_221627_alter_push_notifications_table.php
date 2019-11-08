<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->string('body', 165)->change();
            $table->renameColumn('body', 'content');
            $table->dropColumn('title');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('push_notifications', function (Blueprint $table) {
            $table->renameColumn('content', 'body');
            $table->string('title')->nullable();
        });
    }
}
