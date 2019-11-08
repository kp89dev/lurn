<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdAndSubjectToEmailStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_statuses', function(Blueprint $table){
            $table->integer('user_id')->after('aws_id');
            $table->string('subject')->after('status');

            $table->integer('workflow_id')->default(-1)->change();
            $table->integer('step')->default(-1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_statuses', function(Blueprint $table){
            $table->dropColumn(['user_id', 'subject']);

            $table->integer('workflow_id')->change();
            $table->integer('step')->change();
        });
    }
}
