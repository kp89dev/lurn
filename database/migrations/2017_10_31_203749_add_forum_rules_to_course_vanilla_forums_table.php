<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForumRulesToCourseVanillaForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_vanilla_forums', function (Blueprint $table) {
            $table->longText('forum_rules')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('course_vanilla_forums', 'forum_rules')) {
            Schema::table('course_vanilla_forums', function (Blueprint $table) {
                $table->dropColumn('forum_rules');
            });
        }
    }
}
