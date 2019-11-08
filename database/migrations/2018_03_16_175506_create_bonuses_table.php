<?php

use App\Models\Bonus;
use App\Models\Course;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id');
            $table->integer('points_required');
            $table->timestamps();
        });

        // Create a bonus from the first available course in the database.
        if ($course = Course::first()) {
            Bonus::truncate();
            Bonus::create([
                'course_id'       => $course->id,
                'points_required' => 2000,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonuses');
    }
}
