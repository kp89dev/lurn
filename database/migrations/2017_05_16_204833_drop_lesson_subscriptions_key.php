<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLessonSubscriptionsKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lesson_subscriptions', function(Blueprint $table){
            $table->dropUnique('lesson_subscriptions_lesson_id_unique');
            $table->dropUnique('lesson_subscriptions_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('lesson_subscriptions', function(Blueprint $table) {
//            $table->dropUnique('unique_user_to_lesson');
//        });
    }
}
