<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromTableToCourseSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::table('course_subscriptions', function(Blueprint $table){
            $table->string('from_table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_subscriptions', function(Blueprint $table){
            $table->dropColumn('from_table');
        });
    }
}
