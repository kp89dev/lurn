<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('test_id')->unsigned();
            $table->string('certificate_title');
            $table->string('certificate_image');
            $table->string('certificate_image1')->nullable();
            $table->string('certificate_image2')->nullable();
            $table->text('certificate_style')->nullable();
            $table->longtext('certificate_body');
            $table->date('issued');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('test_id')->references('id')->on('tests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_certificates');
    }
}
