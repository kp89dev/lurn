<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMerchantIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_infusionsoft', function(Blueprint $table) {
            $table->tinyInteger('is_merchant_id')->after('is_account')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('course_infusionsoft', 'is_merchant_id')) {
            Schema::table('course_infusionsoft', function (Blueprint $table) {
                $table->dropColumn('is_merchant_id');
            });
        }
    }
}
