<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCourseInfusionsoftAddSubscriptionPaymentFormColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_infusionsoft', function (Blueprint $table) {
            $table->longText('subscription_payment_url')->nullable()->after('is_merchant_id');
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
            $table->dropColumn('subscription_payment_url');
        });
    }
}
