<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequiredSubscriptionBasedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_courses', function (Blueprint $table) {
            $table->dropColumn('expired_at');
            $table->boolean('subscription_payment')->default(0)->after('course_infusionsoft_id');
            $table->integer('payments_required')->nullable()->after('payments_made');
            $table->integer('is_product_id')->after('course_infusionsoft_id')->nullable()
                ->comment('Used to store the IS product ID at the time of enrollment (it can be subscription product id or one time payment product id).');
        });

        Schema::table('course_infusionsoft', function (Blueprint $table) {
            $table->integer('payments_required')->nullable()->after('subscription_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_courses', function (Blueprint $table) {
            $table->timestamp('expired_at')->nullable()->after('updated_at');
            $table->dropColumn(['payments_required', 'subscription_payment', 'is_product_id']);
        });

        Schema::table('course_infusionsoft', function (Blueprint $table) {
            $table->dropColumn('payments_required');
        });
    }
}
