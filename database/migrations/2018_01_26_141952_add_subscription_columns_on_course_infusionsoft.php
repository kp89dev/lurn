<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubscriptionColumnsOnCourseInfusionsoft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_infusionsoft', function (Blueprint $table) {
            $table->decimal('subscription_price')->after('subscription')->nullable();
            $table->integer('is_subscription_product_id')->after('is_product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_infusionsoft', function (Blueprint $table) {
            $table->dropColumn(['subscription_price', 'is_subscription_product_id']);
        });
    }
}
