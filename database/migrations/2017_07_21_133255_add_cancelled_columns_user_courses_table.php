<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelledColumnsUserCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_courses', function(Blueprint $table){
            $table->unsignedInteger('cancelled_by')->after('cancelled_at')->nullable();
            $table->string('cancelled_reason')->after('cancelled_at')->nullable();
        });

        DB::unprepared('ALTER TABLE user_courses MODIFY status tinyint(4);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
