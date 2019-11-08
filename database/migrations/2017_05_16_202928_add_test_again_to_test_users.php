<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestAgainToTestUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_users', function(Blueprint $table){
            $table->unsignedTinyInteger('test_again');
            $table->unsignedTinyInteger('status')
                ->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_users', function(Blueprint $table){
            $table->dropColumn(['test_again', 'status']);
        });
    }
}
