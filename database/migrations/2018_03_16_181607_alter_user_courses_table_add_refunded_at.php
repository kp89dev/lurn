<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserCoursesTableAddRefundedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('user_courses', 'refunded_at')) {
            Schema::table('user_courses', function (Blueprint $table) {
                $table->dateTime('refunded_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_courses', 'refunded_at')) {
            Schema::table('user_courses', function (Blueprint $table) {
                $table->dropColumn('refunded_at');
            });
        }
    }
}
