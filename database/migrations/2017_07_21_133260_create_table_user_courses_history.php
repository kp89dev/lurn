<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserCoursesHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE TABLE `user_courses_history` LIKE `user_courses`');

        DB::unprepared(<<<SQL
CREATE TRIGGER `user_courses_update` BEFORE UPDATE ON `user_courses` FOR EACH ROW
    BEGIN
        INSERT INTO `user_courses_history`  
        VALUES (null, OLD.`user_id`, OLD.`course_id`, OLD.`status`, OLD.`invoice_id`, OLD.`paid_at`, OLD.`cancelled_at`, OLD.`cancelled_by`, OLD.`cancelled_reason`, OLD.`created_at`, OLD.`updated_at`);
    END
SQL
);

        DB::unprepared(<<<SQL
CREATE TRIGGER `user_courses_delete` BEFORE DELETE ON `user_courses` FOR EACH ROW
    BEGIN
        INSERT INTO `user_courses_history`  
        VALUES (null, OLD.`user_id`, OLD.`course_id`, OLD.`status`, OLD.`invoice_id`, OLD.`paid_at`, OLD.`cancelled_at`, OLD.`cancelled_by`, OLD.`cancelled_reason`, OLD.`created_at`, OLD.`updated_at`);
    END
SQL
);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_courses_history');
        DB::unprepared('DROP TRIGGER IF EXISTS `user_courses_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `user_courses_delete`');
    }
}
