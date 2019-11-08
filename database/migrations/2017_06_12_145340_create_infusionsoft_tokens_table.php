<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfusionsoftTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infusionsoft_tokens', function(Blueprint $table) {
            $table->increments('id');
            $table->char('account', 5);
            $table->string('access_token');
            $table->string('refresh_token');
            $table->integer('end_of_life');

            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('infusionsoft_tokens');
    }
}
