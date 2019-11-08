<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowEmailStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_email_statuses', function(Blueprint $table) {
            $table->increments('id');
            $table->char('aws_id', 60)->unique();
            $table->integer('workflow_id');
            $table->integer('step');
            $table->integer('status');
            $table->dateTime('last_timestamp')->useCurrent = true;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_email_statuses');
    }
}
