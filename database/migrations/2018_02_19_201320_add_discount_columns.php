<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_infusionsoft', function (Blueprint $table) {
            $table->unsignedInteger('is_subscription_discount_product_id')->nullable()->after('subscription_payment_url');
            $table->text('is_subscription_discount_product_url')->nullable()->after('is_subscription_discount_product_id');
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
            $table->dropColumn('is_subscription_discount_product_id');
            $table->dropColumn('is_subscription_discount_product_url');
        });
    }
}
