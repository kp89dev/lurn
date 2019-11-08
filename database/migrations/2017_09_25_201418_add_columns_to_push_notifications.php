<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPushNotifications extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dateTime('start_utc');
            $table->dateTime('end_utc');
            $table->boolean('all_visitors')->default(false);
            $table->string('title');
            $table->text('body');
            $table->string('cta_type');
            $table->string('internal_cta_type')->nullable();
            $table->string('internal_course_slug')->nullable();
            $table->integer('internal_news_slug')->nullable();
            $table->string('internal_link')->nullable();
            $table->string('external_link')->nullable();
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
            $table->dropColumn(['start_utc', 'end_utc', 'all_visitors', 'title', 'body', 'cta_type', 'internal_cta_type', 'internal_course_slug', 'internal_news_slug','internal_link','external_link']);
        });
    }
}
