<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundTrackerHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_tracker_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('refund_tracker_id');
            $table->string('activity');
            $table->string('error_message')->nullable();
            $table->timestamps();

            $table->foreign('refund_tracker_id')->references('id')->on('refund_trackers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refund_tracker_histories');
    }
}
