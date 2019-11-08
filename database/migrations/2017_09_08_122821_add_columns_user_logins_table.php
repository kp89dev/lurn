<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsUserLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_logins', function(Blueprint $table){
            $table->text('as')->after('user_agent')->nullable();
            $table->string('city')->after('as')->nullable();
            $table->string('country')->after('city')->nullable();
            $table->string('countryCode')->after('country')->nullable();
            $table->string('isp')->after('countryCode')->nullable();
            $table->string('org')->after('isp')->nullable();
            $table->string('region')->after('org')->nullable();
            $table->string('regionName')->after('region')->nullable();
            $table->string('timezone')->after('regionName')->nullable();
            $table->string('zip')->after('timezone')->nullable();
        });
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
