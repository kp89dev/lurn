<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserMerged extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_merges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merged_user_id');
            $table->string('from_table');
            $table->unsignedInteger('into_user_id');

            $table->timestamps();
            $table->unique(['id', 'merged_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_merges');
    }
}
