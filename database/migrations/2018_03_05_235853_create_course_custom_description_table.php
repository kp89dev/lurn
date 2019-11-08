<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Seeders\DescriptionTypesSeeder;

class CreateCourseCustomDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_custom_description', function (Blueprint $table) {
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('custom_description_id');
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('courses');

            $table->foreign('custom_description_id')
                ->references('id')
                ->on('custom_descriptions');
        });

        app(DescriptionTypesSeeder::class)->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_custom_description');
    }
}
