<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_users', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('test_id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('result');
            $table->decimal('mark', 5, 2);
            $table->integer('no_of_tries');
            $table->text('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('test_users');
    }
}
