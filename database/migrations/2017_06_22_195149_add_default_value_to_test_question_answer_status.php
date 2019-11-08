<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValueToTestQuestionAnswerStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_question_answers', function(Blueprint $table){
            $table->integer('status')->default(1)->change();
            $table->integer('order')->default(0)->change();
            $table->integer('is_answer')->default(0)->change();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_question_answers', function(Blueprint $table){
            $table->integer('status')->change();
            $table->integer('order')->change();
            $table->integer('is_answer')->change();
        });
    }
}
