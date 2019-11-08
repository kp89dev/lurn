<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBadgesImportAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_badges_import_all', function (Blueprint $table){
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('badge_id');
            $table->text('badge_request_urls');
            $table->text('comment');
            $table->tinyInteger('status');
            $table->string('connection');

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
        Schema::dropIfExists('user_badges_import_all');
    }
}
